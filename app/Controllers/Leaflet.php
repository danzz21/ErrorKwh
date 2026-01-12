<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LocationTrackingModel;

class Tracking extends BaseController
{
    protected $trackingModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->trackingModel = new LocationTrackingModel();
        $this->userModel = new UserModel();
        
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    public function leaflet()
    {
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        // Get all active users
        if ($role === 'admin') {
            $users = $this->userModel->where('is_active', 1)->findAll();
        } else {
            $users = $this->userModel->where('id', $userId)->findAll();
        }
        
        $data = [
            'title' => 'Live Tracking PLN - Leaflet',
            'users' => $users,
            'user_role' => $role,
            'default_lat' => -6.2088,  // Jakarta
            'default_lng' => 106.8456
        ];
        
        return view('tracking/leaflet', $data);
    }
    
    // API untuk get lokasi (sama seperti sebelumnya)
    public function getLiveLocations()
    {
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        $query = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, users.unit_kerja, users.foto, users.role')
            ->join('users', 'users.id = location_tracking.user_id')
            ->where('users.is_active', 1);
        
        if ($role !== 'admin') {
            $query->where('location_tracking.user_id', $userId);
        }
        
        $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $query->where('location_tracking.timestamp >=', $fiveMinutesAgo);
        
        $locations = $query->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $locations,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}