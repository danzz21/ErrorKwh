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
        
        // Auto-track user location saat akses KWH
        if (session()->get('isLoggedIn') && session()->get('role') !== 'admin') {
            $this->trackUserActivity();
        }
    }

private function trackUserActivity()
{
    try {
        $trackingModel = new \App\Models\LocationTrackingModel();
        $userId = session()->get('user_id');
        
        // Cek kapan terakhir tracking (jangan terlalu sering)
        $lastTrack = $trackingModel->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        // Jika lebih dari 30 menit yang lalu, track lagi
        if (!$lastTrack || strtotime($lastTrack['timestamp']) < time() - 1800) {
            $data = [
                'user_id' => $userId,
                'latitude' => -6.2088 + (rand(-50, 50) / 1000), // Random sekitar Jakarta
                'longitude' => 106.8456 + (rand(-50, 50) / 1000),
                'accuracy' => rand(100, 1000),
                'address' => 'Sedang mengakses KWH Calculator',
                'device_info' => 'Web App - KWH Calculator',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $trackingModel->insert($data);
        }
    } catch (\Exception $e) {
        // Silent fail
    }
}  
    
    public function index()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        
        // Get mode from session or default to mode1
        $mode = session()->get('kwh_mode') ?? 'mode1';
        
        // Get data berdasarkan role
        if ($role === 'admin') {
            $logs = $this->kwhModel->select('kwh_data.*, users.nama as operator_nama, users.unit_kerja')
                ->join('users', 'users.id = kwh_data.user_id', 'left')
                ->orderBy('kwh_data.created_at', 'DESC')
                ->findAll();
        } else {
            $logs = $this->kwhModel->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }
        
        $data = [
            'title' => 'KWH Error Calculator',
            'logs' => $logs,
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'mode' => $mode
        ];
        
        return view('kwh/index', $data);
    }
     public function switchMode()
    {
        $mode = $this->request->getGet('mode');
        if (in_array($mode, ['mode1', 'mode2'])) {
            session()->set('kwh_mode', $mode);
        }
        
        return redirect()->to(base_url('kwh'));
    }
    public function save()
    {
        // Validation
        $validation = \Config\Services::validation();
        
        $mode = session()->get('kwh_mode') ?? 'mode1';
        
        // Set validation rules berdasarkan mode
        if ($mode === 'mode1') {
            $validation->setRules([
                'keterangan' => 'required|min_length[3]',
                'arus' => 'required|numeric',
                'constanta' => 'required|numeric',
                'class_meter' => 'required',
                'count' => 'required|numeric',
                'duration' => 'required|numeric'
            ]);
        } else {
            $validation->setRules([
                'keterangan' => 'required|min_length[3]',
                'class_meter' => 'required',
                'p1_input' => 'required|numeric', // P1 manual
                'pr_input' => 'required|numeric', // Pr
                'ps_input' => 'required|numeric', // Ps
                'pt_input' => 'required|numeric'  // Pt
            ]);
        }
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(', ', $validation->getErrors()));
        }
        
        // Get POST data
        $post = $this->request->getPost();
        
        // Handle class meter validation
        $classMeterValue = $post['class_meter'];
        $allowedClasses = ['1.0', '0.5', '0.2', '1', '0.5', '0.2'];
        $classMeterStr = (string)$classMeterValue;
        $validClass = false;
        
        foreach ($allowedClasses as $allowed) {
            if ((string)$allowed === $classMeterStr) {
                $validClass = true;
                break;
            }
        }
        
        if (!$validClass) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kelas meter harus 1.0, 0.5, atau 0.2');
        }
        
        $classMeter = (float)$classMeterValue;
        
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
        
        // Calculate values berdasarkan mode
        if ($mode === 'mode1') {
            // Mode 1: Hitung dari kedipan
            $p1 = $this->calculateP1_mode1($post);
            $p2 = $this->calculateP2_mode1($post);
            
            $baseData = [
                'arus' => $post['arus'],
                'tegangan' => $post['tegangan'] ?? 220,
                'cosphi' => $post['cosphi'] ?? 0.85,
                'constanta' => $post['constanta'],
                'count' => $post['count'] ?? 0,
                'duration' => $post['duration'] ?? 0,
                'blink_data' => $post['blink_data'] ?? '[]',
                'selected_blink' => $post['selected_blink'] ?? 1,
            ];
        } else {
            // Mode 2: Input manual 3 phase
            $p1 = (float)$post['p1_input'];
            $pr = (float)$post['pr_input'];
            $ps = (float)$post['ps_input'];
            $pt = (float)$post['pt_input'];
            $p2 = $pr + $ps + $pt; // Total P2 dari 3 phase
            
            $baseData = [
                'arus' => 0, // Tidak digunakan di mode 2
                'tegangan' => 0,
                'cosphi' => 0,
                'constanta' => 0,
                'count' => 0,
                'duration' => 0,
                'blink_data' => '[]',
                'selected_blink' => 0,
                'pr_value' => $pr, // Simpan nilai terpisah
                'ps_value' => $ps,
                'pt_value' => $pt
            ];
        }
        
        $errorPercent = $this->calculateError($p1, $p2);
        $statusFinal = $this->determineFinalStatus($errorPercent, $classMeter);
        
        // Prepare data untuk database
        $data = array_merge($baseData, [
            'keterangan' => $post['keterangan'],
            'class_meter' => $classMeter,
            'p1_kw' => $p1,
            'p2_kw' => $p2,
            'error_percent' => $errorPercent,
            'status_final' => $statusFinal,
            'calculation_mode' => $mode,
            'photos' => json_encode($photoNames),
            'user_id' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Save to database
        if ($this->kwhModel->insert($data)) {
            return redirect()->to(base_url('kwh'))
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
        
        if ($absError > 5) {
            return 'BURUK';
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
    
     private function calculateP1_mode1($data)
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
    
     private function calculateP2_mode1($data)
    {
        $arus = (float)$data['arus'];
        $tegangan = (float)($data['tegangan'] ?? 220);
        $cosphi = (float)($data['cosphi'] ?? 0.85);
        
        return ($tegangan * $arus * $cosphi) / 1000;
    }
     private function calculateP2_mode2($data)
    {
        $arus = (float)$data['arus'];
        
        // Untuk demo, kita hitung P2 berdasarkan rumus yang diberikan
        // Pr = 4.675, Ps = 4.488, Pt = 5.423 (dalam kW)
        // P2_total = Pr + Ps + Pt = 14.586 kW
        
        // Dalam mode 2, kita anggap P2 sudah ditentukan
        // Atau kita bisa hitung berdasarkan faktor tertentu
        
        // CONTOH 1: Jika arus 5A, maka P2 = 14.586 kW (fixed)
        // return 14.586;
        
        // CONTOH 2: Jika arus berubah, kita skala berdasarkan arus
        // Asumsi: pada arus 5A menghasilkan 14.586 kW
        // Maka rumus: P2 = (arus / 5) * 14.586
        
        $baseCurrent = 5.0; // Arus dasar 5A
        $basePower = 14.586; // Daya pada arus 5A
        
        return ($arus / $baseCurrent) * $basePower;
    }
    
    private function calculateError($p1, $p2)
    {
        if ($p2 == 0) {
            return 0;
        }
        
        return (($p1 - $p2) / $p2) * 100;
    }
}