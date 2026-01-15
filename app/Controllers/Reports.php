<?php

namespace App\Controllers;

use App\Models\KwhModel;
use App\Models\UserModel;
use App\Models\LocationTrackingModel;

class Reports extends BaseController
{
    protected $kwhModel;
    protected $userModel;
    protected $trackingModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        $this->userModel = new UserModel();
        $this->trackingModel = new LocationTrackingModel();
        
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    public function index()
    {
        $role = session()->get('role');
        
        $data = [
            'title' => 'Reports - PLN',
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'current_date' => date('Y-m-d'),
            'start_date' => date('Y-m-01'), // First day of current month
            'end_date' => date('Y-m-t')     // Last day of current month
        ];
        
        // Jika admin, tambahkan stats
        if ($role === 'admin') {
            $data['total_kwh'] = $this->kwhModel->countAll();
            $data['total_users'] = $this->userModel->countAll();
            $data['total_tracks'] = $this->trackingModel->countAll();
        }
        
        return view('reports/index', $data);
    }
    
    public function kwh()
    {
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $userIdFilter = $this->request->getGet('user_id');
        
        $kwhModel = $this->kwhModel
            ->select('kwh_data.*, users.nama as operator_nama, users.unit_kerja');
        
        // Apply filters
        $kwhModel->where('DATE(kwh_data.created_at) >=', $startDate);
        $kwhModel->where('DATE(kwh_data.created_at) <=', $endDate);
        
        // Filter by user if specified and admin
        if ($userIdFilter && $role === 'admin') {
            $kwhModel->where('kwh_data.user_id', $userIdFilter);
        } elseif ($role !== 'admin') {
            // Non-admin hanya bisa lihat data sendiri
            $kwhModel->where('kwh_data.user_id', $userId);
        }
        
        $kwhModel->join('users', 'users.id = kwh_data.user_id')
                 ->orderBy('kwh_data.created_at', 'DESC');
        
        $data = [
            'title' => 'KWH Reports - PLN',
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'kwh_data' => $kwhModel->findAll(),
            'users' => $role === 'admin' ? $this->userModel->findAll() : [],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_user' => $userIdFilter
        ];
        
        return view('reports/kwh', $data);
    }
    
    public function tracking()
    {
        $role = session()->get('role');
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $userIdFilter = $this->request->getGet('user_id');
        
        $trackingModel = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, users.unit_kerja');
        
        // Apply filters
        $trackingModel->where('DATE(location_tracking.timestamp) >=', $startDate);
        $trackingModel->where('DATE(location_tracking.timestamp) <=', $endDate);
        
        // Filter by user if specified and admin
        if ($userIdFilter && $role === 'admin') {
            $trackingModel->where('location_tracking.user_id', $userIdFilter);
        }
        
        $trackingModel->join('users', 'users.id = location_tracking.user_id')
                      ->orderBy('location_tracking.timestamp', 'DESC')
                      ->limit(500);
        
        $data = [
            'title' => 'Tracking Reports - PLN',
            'user_role' => $role,
            'user_nama' => session()->get('nama'),
            'locations' => $trackingModel->findAll(),
            'users' => $role === 'admin' ? $this->userModel->findAll() : [],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_user' => $userIdFilter
        ];
        
        return view('reports/tracking', $data);
    }
    
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'kwh';
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        if ($type === 'kwh') {
            $this->exportKwhReport($startDate, $endDate);
        } else {
            $this->exportTrackingReport($startDate, $endDate);
        }
    }
    
    private function exportKwhReport($startDate, $endDate)
    {
        $data = $this->kwhModel
            ->select('kwh_data.*, users.nama as operator_nama, users.nip, users.unit_kerja')
            ->join('users', 'users.id = kwh_data.user_id')
            ->where('DATE(kwh_data.created_at) >=', $startDate)
            ->where('DATE(kwh_data.created_at) <=', $endDate)
            ->orderBy('kwh_data.created_at', 'DESC')
            ->findAll();
        
        $filename = "kwh_report_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'ID', 'Date', 'Time', 'Customer Name', 'Customer ID', 
            'Location', 'Operator', 'Unit', 'Current (A)', 'Voltage (V)',
            'Cos φ', 'Constanta', 'Blink Count', 'Duration (s)', 
            'P1 (kW)', 'P2 (kW)', 'Error (%)', 'Meter Class (%)', 'Status'
        ]);
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                date('Y-m-d', strtotime($row['created_at'])),
                date('H:i:s', strtotime($row['created_at'])),
                $row['nama_pelanggan'] ?? '-',
                $row['id_pelanggan'] ?? '-',
                $row['keterangan'],
                $row['operator_nama'],
                $row['unit_kerja'],
                number_format($row['arus'], 2),
                number_format($row['tegangan'], 2),
                number_format($row['cosphi'], 2),
                number_format($row['constanta'], 0),
                $row['count'],
                number_format($row['duration'], 2),
                number_format($row['p1_kw'], 3),
                number_format($row['p2_kw'], 3),
                number_format($row['error_percent'], 2),
                number_format($row['class_meter'], 1),
                $row['status_final']
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    private function exportTrackingReport($startDate, $endDate)
    {
        $data = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, users.unit_kerja')
            ->join('users', 'users.id = location_tracking.user_id')
            ->where('DATE(location_tracking.timestamp) >=', $startDate)
            ->where('DATE(location_tracking.timestamp) <=', $endDate)
            ->orderBy('location_tracking.timestamp', 'DESC')
            ->findAll();
        
        $filename = "tracking_report_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'ID', 'Date', 'Time', 'User', 'NIP', 'Unit', 
            'Latitude', 'Longitude', 'Accuracy (m)', 'Address',
            'Device Info', 'Status'
        ]);
        
        // Data
        foreach ($data as $row) {
            $timeDiff = time() - strtotime($row['timestamp']);
            $status = ($timeDiff < 300) ? 'Online' : 'Offline';
            
            fputcsv($output, [
                $row['id'],
                date('Y-m-d', strtotime($row['timestamp'])),
                date('H:i:s', strtotime($row['timestamp'])),
                $row['nama'],
                $row['nip'],
                $row['unit_kerja'],
                number_format($row['latitude'], 6),
                number_format($row['longitude'], 6),
                number_format($row['accuracy'], 0),
                $row['address'] ?? '',
                $row['device_info'] ?? 'Web',
                $status
            ]);
        }
        
        fclose($output);
        exit();
    }
}