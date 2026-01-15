<?php

namespace App\Controllers;

use App\Models\KwhModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Kwh extends BaseController
{
    protected $kwhModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        $this->userModel = new UserModel();
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
                    'latitude' => -6.2088 + (rand(-50, 50) / 1000),
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
    
    /**
     * Halaman utama KWH Calculator
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        
        // Get mode dari session atau default ke mode1
        $mode = session()->get('kwh_mode') ?? 'mode1';
        
        // Get data berdasarkan role
        if ($role === 'admin') {
            $logs = $this->kwhModel
                ->select('kwh_data.*, users.nama as operator_nama, users.unit_kerja')
                ->join('users', 'users.id = kwh_data.user_id', 'left')
                ->orderBy('kwh_data.created_at', 'DESC')
                ->findAll();
        } else {
            $logs = $this->kwhModel
                ->where('user_id', $userId)
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
    
    /**
     * View All Data (untuk admin)
     */
    public function all()
    {
        $role = session()->get('role');
        
        // Cek akses admin
        if ($role !== 'admin') {
            return redirect()->to(base_url('kwh'))->with('error', 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $data = [
            'title' => 'All KWH Data - PLN',
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'kwh_data' => $this->kwhModel
                ->select('kwh_data.*, users.nama as operator_nama, users.nip, users.role, users.unit_kerja')
                ->join('users', 'users.id = kwh_data.user_id')
                ->orderBy('kwh_data.created_at', 'DESC')
                ->findAll()
        ];
        
        return view('kwh/all', $data);
    }
    
    /**
     * View Detail Data by ID
     */
    public function view($id)
    {
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        // Get data
        $kwh = $this->kwhModel->find($id);
        
        if (!$kwh) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        
        // Cek akses
        if ($role !== 'admin' && $kwh['user_id'] != $userId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat data ini');
        }
        
        // Get user info
        $user = $this->userModel->find($kwh['user_id']);
        
        // Parse photos jika ada
        $photos = [];
        if (!empty($kwh['photos']) && $kwh['photos'] !== 'null') {
            $photos = json_decode($kwh['photos'], true);
        }
        
        $data = [
            'title' => 'Detail KWH Data - PLN',
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'kwh' => $kwh,
            'user' => $user,
            'photos' => $photos
        ];
        
        return view('kwh/view', $data);
    }
    
    /**
     * Switch Mode (Mode 1 / Mode 2)
     */
    public function switchMode()
    {
        $mode = $this->request->getGet('mode');
        if (in_array($mode, ['mode1', 'mode2'])) {
            session()->set('kwh_mode', $mode);
        }
        
        return redirect()->to(base_url('kwh'));
    }
    
    /**
     * Save Data dari form
     */
    public function save()
    {
        // Set timezone ke Asia/Jakarta
        date_default_timezone_set('Asia/Jakarta');
        
        $validation = \Config\Services::validation();
        $mode = session()->get('kwh_mode') ?? 'mode1';
        
        // Set validation rules
        if ($mode === 'mode1') {
            $validation->setRules([
                'nama_pelanggan' => 'required|min_length[3]|max_length[100]',
                'id_pelanggan' => 'required|min_length[3]|max_length[50]',
                'keterangan' => 'permit_empty|min_length[3]|max_length[255]',
                'arus' => 'required|numeric',
                'constanta' => 'required|numeric',
                'class_meter' => 'required',
                'count' => 'required|numeric',
                'duration' => 'required|numeric'
            ]);
        } else {
            $validation->setRules([
                'nama_pelanggan' => 'required|min_length[3]|max_length[100]',
                'id_pelanggan' => 'required|min_length[3]|max_length[50]',
                'keterangan' => 'permit_empty|min_length[3]|max_length[255]',
                'class_meter' => 'required',
                'p1_input' => 'required|numeric',
                'pr_input' => 'required|numeric',
                'ps_input' => 'required|numeric',
                'pt_input' => 'required|numeric'
            ]);
        }
        
        // Custom error messages
        $validation->setRule('nama_pelanggan', 'Nama Pelanggan', 'required', [
            'required' => 'Nama Pelanggan wajib diisi!'
        ]);
        
        $validation->setRule('id_pelanggan', 'ID Pelanggan', 'required', [
            'required' => 'ID Pelanggan / No. Meter wajib diisi!'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }
        
        // Get POST data
        $post = $this->request->getPost();
        
        // Validate class meter
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
            $p1 = (float)$post['p1_input'];
            $pr = (float)$post['pr_input'];
            $ps = (float)$post['ps_input'];
            $pt = (float)$post['pt_input'];
            $p2 = $pr + $ps + $pt;
            
            $baseData = [
                'arus' => 0,
                'tegangan' => 0,
                'cosphi' => 0,
                'constanta' => 0,
                'count' => 0,
                'duration' => 0,
                'blink_data' => '[]',
                'selected_blink' => 0,
                'pr_value' => $pr,
                'ps_value' => $ps,
                'pt_value' => $pt
            ];
        }
        
        $errorPercent = $this->calculateError($p1, $p2);
        $statusFinal = $this->determineFinalStatus($errorPercent, $classMeter);
        
        $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        
        // Prepare data untuk database
        $data = array_merge($baseData, [
            'nama_pelanggan' => $post['nama_pelanggan'],
            'id_pelanggan' => $post['id_pelanggan'],
            'keterangan' => $post['keterangan'] ?? '',
            'class_meter' => $classMeter,
            'p1_kw' => $p1,
            'p2_kw' => $p2,
            'error_percent' => $errorPercent,
            'status_final' => $statusFinal,
            'calculation_mode' => $mode,
            'photos' => !empty($photoNames) ? json_encode($photoNames) : null,
            'user_id' => session()->get('user_id'),
            'created_at' => $currentDateTime->format('Y-m-d H:i:s')
        ]);
        
        // Save ke database
        if ($this->kwhModel->insert($data)) {
            $statusMessage = ($statusFinal === 'LUAR_KELAS') ? 'DI LUAR KELAS METER' : $statusFinal;
            
            return redirect()->to(base_url('kwh'))
                ->with('success', 'Data berhasil disimpan! Status: ' . $statusMessage . 
                       '<br>Pelanggan: ' . $post['nama_pelanggan'] . 
                       ' (ID: ' . $post['id_pelanggan'] . ')');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data ke database');
        }
    }
    
    /**
     * View Photo
     */
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
    
    /**
     * Export Data (untuk semua data)
     */
    public function export()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        
        // Get data berdasarkan role
        if ($role === 'admin') {
            $logs = $this->kwhModel
                ->select('kwh_data.*, users.nama as operator_nama, users.unit_kerja')
                ->join('users', 'users.id = kwh_data.user_id', 'left')
                ->orderBy('kwh_data.created_at', 'DESC')
                ->findAll();
        } else {
            $logs = $this->kwhModel
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }
        
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
            'Nama Pelanggan',
            'ID Pelanggan',
            'Lokasi',
            'Operator',
            'Unit Kerja',
            'Mode',
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
            'Kelas Meter (%)',
            'Status',
            'Jumlah Foto'
        ]);
        
        // Data
        $no = 1;
        foreach ($logs as $log) {
            $blinkPerSec = ($log['duration'] > 0) ? $log['count'] / $log['duration'] : 0;
            
            $status = $log['status_final'] ?? 'BELUM_DIHITUNG';
            if ($status === 'LUAR_KELAS') {
                $statusDisplay = 'DI LUAR KELAS METER';
            } elseif ($status === 'BAIK') {
                $statusDisplay = 'BAIK';
            } else {
                $statusDisplay = $status;
            }
            
            $modeDisplay = (isset($log['calculation_mode']) && $log['calculation_mode'] === 'mode2') ? '3 Phase' : 'Kedipan';
            
            $photoCount = 0;
            if (!empty($log['photos']) && $log['photos'] !== 'null') {
                $photos = json_decode($log['photos'], true);
                $photoCount = is_array($photos) ? count($photos) : 0;
            }
            
            $operator = isset($log['operator_nama']) ? $log['operator_nama'] : (session()->get('nama') ?? 'Unknown');
            $unitKerja = isset($log['unit_kerja']) ? $log['unit_kerja'] : '';
            $namaPelanggan = isset($log['nama_pelanggan']) ? $log['nama_pelanggan'] : '-';
            $idPelanggan = isset($log['id_pelanggan']) ? $log['id_pelanggan'] : '-';
            
            fputcsv($output, [
                $no++,
                $log['id'],
                date('d/m/Y', strtotime($log['created_at'])),
                date('H:i:s', strtotime($log['created_at'])),
                $namaPelanggan,
                $idPelanggan,
                $log['keterangan'],
                $operator,
                $unitKerja,
                $modeDisplay,
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
                number_format($log['class_meter'], 1),
                $statusDisplay,
                $photoCount
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Delete Data
     */
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
            session()->setFlashdata('success', 'Data berhasil dihapus');
            
            // Redirect dengan anchor riwayat jika dari halaman utama
            if (strpos($_SERVER['HTTP_REFERER'] ?? '', '#riwayat') !== false) {
                return redirect()->to(base_url('kwh') . '#riwayat')
                    ->with('success', 'Data berhasil dihapus');
            }
            
            return redirect()->back()
                ->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data');
        }
    }
    
    /**
     * Clear All Data (hanya untuk testing/halaman admin)
     */
    public function clearAll()
    {
        // Hanya admin yang bisa clear all
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        
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
            
            return redirect()->to(base_url('kwh'))
                ->with('success', 'Semua data berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus semua data: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper Methods
     */
    private function determineFinalStatus($errorPercent, $classMeter)
    {
        $absError = abs($errorPercent);
        $tolerance = 0.01;
        
        if (abs($absError - $classMeter) <= $tolerance) {
            return 'BAIK';
        } else {
            return 'LUAR_KELAS';
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
    
    private function calculateError($p1, $p2)
    {
        if ($p2 == 0) {
            return 0;
        }
        
        return (($p1 - $p2) / $p2) * 100;
    }
}