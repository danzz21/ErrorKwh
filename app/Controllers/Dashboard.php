<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KwhModel;
use App\Models\LocationTrackingModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $kwhModel;
    protected $trackingModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->kwhModel = new KwhModel();
        $this->trackingModel = new LocationTrackingModel();
        
        // Cek login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    public function index()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        $nama = session()->get('nama');
        
        // Data dasar untuk semua role
        $data = [
            'title' => 'Dashboard - PLN',
            'user_role' => $role,
            'user_nama' => $nama,
            'current_date' => date('l, d F Y'),
            'current_time' => date('H:i:s')
        ];
        
        // Data untuk admin
        if ($role === 'admin') {
            $data = array_merge($data, $this->getAdminDashboardData());
        } else {
            $data = array_merge($data, $this->getUserDashboardData($userId));
        }
        
        return view('dashboard/index', $data);
    }
    
    private function getAdminDashboardData()
    {
        // Total users
        $totalUsers = $this->userModel->countAll();
        $activeUsers = $this->userModel->where('is_active', 1)->countAllResults();
        
        // KWH Data
        $totalKwhData = $this->kwhModel->countAll();
        $todayKwhData = $this->kwhModel
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
        
        // Tracking Data - online dalam 5 menit terakhir
        $onlineUsers = $this->trackingModel
            ->where('timestamp >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->select('user_id')
            ->groupBy('user_id')
            ->countAllResults();
        
        // Recent KWH Activities
        $recentKwh = $this->kwhModel
            ->select('kwh_data.*, users.nama')
            ->join('users', 'users.id = kwh_data.user_id')
            ->orderBy('kwh_data.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // User Statistics by Role
        $userStats = $this->userModel
            ->select('role, COUNT(*) as count')
            ->groupBy('role')
            ->findAll();
        
        // Recent Locations
        $recentLocations = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, users.role')
            ->join('users', 'users.id = location_tracking.user_id')
            ->orderBy('location_tracking.timestamp', 'DESC')
            ->limit(10)
            ->findAll();
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_kwh_data' => $totalKwhData,
            'today_kwh_data' => $todayKwhData,
            'online_users' => $onlineUsers,
            'recent_kwh' => $recentKwh ?? [],
            'user_stats' => $userStats ?? [],
            'recent_locations' => $recentLocations ?? []
        ];
    }
    
    private function getUserDashboardData($userId)
    {
        // User's KWH Data
        $myKwhData = $this->kwhModel
            ->where('user_id', $userId)
            ->countAllResults();
        
        $todayKwhData = $this->kwhModel
            ->where('user_id', $userId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
        
        // Recent Activities
        $recentActivities = $this->kwhModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Last Location
        $lastLocation = $this->trackingModel
            ->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        return [
            'my_kwh_data' => $myKwhData,
            'today_kwh_data' => $todayKwhData,
            'recent_activities' => $recentActivities ?? [],
            'last_location' => $lastLocation
        ];
    }
    
    // API untuk dashboard stats
    public function getStats()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        
        $role = session()->get('role');
        
        if ($role === 'admin') {
            $stats = [
                'users' => $this->userModel->countAll(),
                'active_users' => $this->userModel->where('is_active', 1)->countAllResults(),
                'kwh_today' => $this->kwhModel
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->countAllResults(),
                'online_now' => $this->trackingModel
                    ->where('timestamp >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
                    ->select('user_id')
                    ->groupBy('user_id')
                    ->countAllResults()
            ];
        } else {
            $userId = session()->get('user_id');
            $stats = [
                'my_kwh_total' => $this->kwhModel->where('user_id', $userId)->countAllResults(),
                'my_kwh_today' => $this->kwhModel
                    ->where('user_id', $userId)
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->countAllResults(),
                'last_activity' => $this->kwhModel
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->first()
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}