<?php

namespace App\Controllers;

use App\Models\KwhModel;
use CodeIgniter\Controller;

class Kwh extends BaseController
{
    protected $kwhModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        helper(['form', 'url', 'text']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'KWH Error Calculator',
            'logs' => $this->kwhModel->orderBy('created_at', 'DESC')->findAll()
        ];
        
        return view('kwh/index', $data);
    }
    
    public function save()
{
    // Validation
    $validation = \Config\Services::validation();
    $validation->setRules([
        'keterangan' => 'required|min_length[3]',
        'arus' => 'required|numeric',
        'constanta' => 'required|numeric',
        'class_meter' => 'required' // HAPUS in_list validation
    ]);
    
    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()
            ->withInput()
            ->with('error', implode(', ', $validation->getErrors()));
    }
    
    // Get POST data
    $post = $this->request->getPost();
    
    // Manual validation untuk class meter
    $classMeter = $post['class_meter'];
    $allowedClasses = ['1.0', '0.5', '0.2', '1', '0.5', '0.2']; // Support both string and float
    
    if (!in_array($classMeter, $allowedClasses)) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Kelas meter harus 1.0, 0.5, atau 0.2');
    }
    
    // Convert to float
    $classMeter = (float)$classMeter;
    
    // Handle photo uploads
    $photoNames = [];
    $photos = $this->request->getFiles('photos');
    
    if ($photos && !empty($photos['photos'])) {
        foreach ($photos['photos'] as $photo) {
            if ($photo->isValid() && !$photo->hasMoved()) {
                $newName = $photo->getRandomName();
                $photo->move(WRITEPATH . 'uploads/kwh', $newName);
                $photoNames[] = $newName;
            }
        }
    }
    
    // Calculate values
    $errorPercent = $this->calculateError($post);
    $classMeter = (float)$post['class_meter'];
    
    // Determine final status based on new logic
    $statusFinal = $this->determineFinalStatus($errorPercent, $classMeter);
    
    // Prepare data
    $data = [
        'keterangan' => $post['keterangan'],
        'arus' => $post['arus'],
        'tegangan' => $post['tegangan'] ?? 220,
        'cosphi' => $post['cosphi'] ?? 0.85,
        'constanta' => $post['constanta'],
        'count' => $post['count'] ?? 0,
        'duration' => $post['duration'] ?? 0,
        'blink_data' => $post['blink_data'] ?? '[]',
        'selected_blink' => $post['selected_blink'] ?? 1,
        'class_meter' => $classMeter,
        'p1_kw' => $this->calculateP1($post),
        'p2_kw' => $this->calculateP2($post),
        'error_percent' => $errorPercent,
        'status_final' => $statusFinal,
        'photos' => json_encode($photoNames),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Save to database
    if ($this->kwhModel->insert($data)) {
        return redirect()->to(base_url())
            ->with('success', 'Data berhasil disimpan! Status: ' . $statusFinal);
    } else {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan data ke database');
    }
}

private function determineFinalStatus($errorPercent, $classMeter)
{
    $absError = abs($errorPercent);
    
    // LOGIKA BARU:
    // Jika error < class meter -> TIDAK STABIL
    // Jika error >= class meter -> BAIK
    // Tapi ada batas maksimum 5% untuk semua kelas
    
    if ($absError > 5) {
        return 'BURUK'; // Selalu buruk jika error > 5%
    }
    
    if ($absError < $classMeter) {
        return 'TIDAK STABIL';
    } else {
        return 'BAIK';
    }
}
    
    public function viewPhoto($filename)
    {
        $path = WRITEPATH . 'uploads/kwh/' . $filename;
        
        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
        exit();
    }
    
   public function export()
{
    $logs = $this->kwhModel->orderBy('created_at', 'DESC')->findAll();
    
    $filename = 'kwh_export_' . date('Ymd_His') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header CSV
    fputcsv($output, [
        'No',
        'ID',
        'Tanggal',
        'Waktu',
        'Lokasi',
        'Arus (A)',
        'Tegangan (V)',
        'Cos φ',
        'Constanta',
        'Kedipan',
        'Durasi (s)',
        'Kedipan/detik',
        'P1 (kW)',
        'P2 (kW)',
        'Error (%)',
        'Status',
        'Jumlah Foto',
        'Catatan'
    ]);
    
    // Data
    $no = 1;
    foreach ($logs as $log) {
        // Hitung kedipan per detik
        $blinkPerSec = ($log['duration'] > 0) ? $log['count'] / $log['duration'] : 0;
        
        // Tentukan status
        $absError = abs($log['error_percent']);
        if ($absError <= 2) {
            $status = 'BAIK';
        } elseif ($absError <= 5) {
            $status = 'WARNING';
        } else {
            $status = 'BURUK';
        }
        
        // Hitung jumlah foto
        $photoCount = 0;
        if (!empty($log['photos']) && $log['photos'] !== 'null') {
            $photos = json_decode($log['photos'], true);
            $photoCount = is_array($photos) ? count($photos) : 0;
        }
        
        fputcsv($output, [
            $no++,
            $log['id'],
            date('d/m/Y', strtotime($log['created_at'])),
            date('H:i:s', strtotime($log['created_at'])),
            $log['keterangan'],
            number_format($log['arus'], 2),
            number_format($log['tegangan'], 2),
            number_format($log['cosphi'], 2),
            number_format($log['constanta'], 0),
            $log['count'],
            number_format($log['duration'], 2),
            number_format($blinkPerSec, 3),
            number_format($log['p1_kw'], 3),
            number_format($log['p2_kw'], 3),
            number_format($log['error_percent'], 2),
            $status,
            $photoCount,
            ''
        ]);
    }
    
    fclose($output);
    exit();
}
    
   public function delete($id)
{
    // Delete associated photos
    $data = $this->kwhModel->find($id);
    if ($data) {
        $photos = json_decode($data['photos'], true);
        if (is_array($photos)) {
            foreach ($photos as $photo) {
                $path = WRITEPATH . 'uploads/kwh/' . $photo;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }
    
    if ($this->kwhModel->delete($id)) {
        // Set flash message dengan JavaScript untuk auto close
        session()->setFlashdata('success', 'Data berhasil dihapus');
        
        // Redirect back to current page dengan anchor
        return redirect()->to(base_url() . '#riwayat')
            ->with('success', 'Data berhasil dihapus');
    } else {
        return redirect()->to(base_url() . '#riwayat')
            ->with('error', 'Gagal menghapus data');
    }
}
    
    public function clearAll()
{
    // Delete all photos
    $uploadPath = WRITEPATH . 'uploads/kwh/';
    if (is_dir($uploadPath)) {
        $files = glob($uploadPath . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    
    // Truncate database table
    try {
        $this->kwhModel->truncate();
        
        // Set success message
        session()->setFlashdata('success', 'Semua data berhasil dihapus');
        
        // Redirect back to current page dengan anchor
        return redirect()->to(base_url() . '#riwayat')
            ->with('success', 'Semua data berhasil dihapus');
            
    } catch (\Exception $e) {
        return redirect()->to(base_url() . '#riwayat')
            ->with('error', 'Gagal menghapus semua data: ' . $e->getMessage());
    }
}
    
    private function calculateP1($data)
    {
        if (empty($data['count']) || empty($data['duration']) || empty($data['constanta'])) {
            return 0;
        }
        
        $count = (float)$data['count'];
        $duration = (float)$data['duration'];
        $constanta = (float)$data['constanta'];
        
        if ($duration == 0 || $constanta == 0) {
            return 0;
        }
        
        $blinkPerSecond = $count / $duration;
        return (3600 * $blinkPerSecond) / $constanta;
    }
    
    private function calculateP2($data)
    {
        $arus = (float)$data['arus'];
        $tegangan = (float)($data['tegangan'] ?? 220);
        $cosphi = (float)($data['cosphi'] ?? 0.85);
        
        return ($tegangan * $arus * $cosphi) / 1000;
    }
    
    private function calculateError($data)
    {
        $p1 = $this->calculateP1($data);
        $p2 = $this->calculateP2($data);
        
        if ($p2 == 0) {
            return 0;
        }
        
        return (($p1 - $p2) / $p2) * 100;
    }
}