<?php

namespace App\Controllers;

use App\Models\KwhModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $kwhModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        $this->userModel = new UserModel();
        
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    public function index()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        
        // Get statistics based on role
        if ($role === 'admin') {
            $totalData = $this->kwhModel->countAll();
            $totalUsers = $this->userModel->countAll();
            $myData = $this->kwhModel->where('user_id', $userId)->countAllResults();
            $recentData = $this->kwhModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
        } else {
            $totalData = $this->kwhModel->where('user_id', $userId)->countAllResults();
            $totalUsers = 1; // Only self
            $myData = $totalData;
            $recentData = $this->kwhModel->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();
        }
        
        $data = [
            'title' => 'Dashboard - PLN KWH Calculator',
            'user' => [
                'nama' => session()->get('nama'),
                'role' => $role,
                'unit_kerja' => session()->get('unit_kerja')
            ],
            'stats' => [
                'total_data' => $totalData,
                'total_users' => $totalUsers,
                'my_data' => $myData
            ],
            'recent_data' => $recentData
        ];
        
        return view('dashboard/index', $data);
    }
}