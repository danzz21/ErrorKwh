<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LocationTrackingModel;
use CodeIgniter\Controller;

class Tracking extends BaseController
{
    protected $trackingModel;
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->trackingModel = new LocationTrackingModel();
        $this->userModel = new UserModel();
        $this->session = session();
        helper(['form', 'url']);
        
        // Check authentication (kecuali untuk API endpoints)
        $uri = service('uri');
        $currentPath = $uri->getPath();
        
        // Routes yang tidak perlu auth
        $publicRoutes = [
            'tracking/updateLocation', 
            'tracking/updateBattery',
            'tracking/heartbeat',
            'tracking/markOffline',
            'tracking/checkStatus'
        ];
        
        $isPublic = false;
        foreach ($publicRoutes as $route) {
            if (strpos($currentPath, $route) === 0) {
                $isPublic = true;
                break;
            }
        }
        
        if (!$isPublic && !$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    /**
     * Main tracking page
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $role = $this->session->get('role');
        
        // Get all active users for admin, only self for regular users
        if ($role === 'admin') {
            $users = $this->userModel->where('is_active', 1)->findAll();
        } else {
            $users = $this->userModel->where('id', $userId)->findAll();
        }
        
        $data = [
            'title' => 'Live Tracking - PLN',
            'user_role' => $role,
            'user_nama' => $this->session->get('nama'),
            'default_lat' => -6.2088,
            'default_lng' => 106.8456
        ];
        
        return view('tracking/index', $data);
    }
    
    /**
     * Heartbeat endpoint - DIPERBARUI
     * Dipanggil setiap 30 detik dari mobile app
     */
   public function heartbeat()
{
    // Set JSON header
    header('Content-Type: application/json');
    
    // Get input
    $input = $this->request->getJSON(true);
    if (!$input) {
        $input = $this->request->getPost();
    }
    
    $userId = $input['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User ID required'
        ]);
        exit();
    }
    
    // Validate token
    if (!empty($input['token'])) {
        $isValid = $this->validateTrackingToken($userId, $input['token']);
        if (!$isValid) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid tracking token'
            ]);
            exit();
        }
    }
    
    try {
        $db = \Config\Database::connect();
        $builder = $db->table('location_tracking');
        
        // Cari record terbaru untuk user ini
        $latest = $builder->select('*')
                         ->where('user_id', $userId)
                         ->orderBy('timestamp', 'DESC')
                         ->get()
                         ->getRowArray();
        
        $now = date('Y-m-d H:i:s');
        
        if ($latest) {
            // Update record terbaru
            $builder->where('id', $latest['id'])
                   ->update([
                       'last_seen' => $now,
                       'status' => 'active',
                       'is_online' => 1
                   ]);
            
            log_message('debug', 'Heartbeat updated for user: ' . $userId);
        } else {
            // Buat record baru jika belum ada
            $builder->insert([
                'user_id' => $userId,
                'latitude' => 0,
                'longitude' => 0,
                'timestamp' => $now,
                'status' => 'active',
                'is_online' => 1,
                'last_seen' => $now,
                'session_id' => $input['session_id'] ?? 'mobile_' . $userId
            ]);
            
            log_message('debug', 'New tracking session created for user: ' . $userId);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Heartbeat updated',
            'timestamp' => $now
        ]);
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Heartbeat error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error'
        ]);
        exit();
    }
}

    
    /**
     * Check user status - DIPERBARUI
     */
    public function checkStatus($userId = null)
    {
        if ($userId === null) {
            $userId = $this->session->get('user_id');
        }
        
        $status = $this->trackingModel->getUserStatus($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'status' => $status['status'],
            'is_online' => $status['is_online'],
            'last_seen' => $status['last_seen'],
            'location' => $status['location'] ?? null
        ]);
    }
    
    public function markOffline()
{
    // Set JSON header
    header('Content-Type: application/json');
    
    // Get input
    $input = $this->request->getJSON(true);
    if (!$input) {
        $input = $this->request->getPost();
    }
    
    $userId = $input['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User ID required'
        ]);
        exit();
    }
    
    try {
        $db = \Config\Database::connect();
        $builder = $db->table('location_tracking');
        
        // Cari record terbaru
        $latest = $builder->select('*')
                         ->where('user_id', $userId)
                         ->orderBy('timestamp', 'DESC')
                         ->get()
                         ->getRowArray();
        
        if ($latest) {
            $builder->where('id', $latest['id'])
                   ->update([
                       'status' => 'offline',
                       'is_online' => 0,
                       'last_seen' => date('Y-m-d H:i:s')
                   ]);
            
            log_message('debug', 'User marked offline: ' . $userId);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'User marked as offline'
        ]);
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Mark offline error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error'
        ]);
        exit();
    }
}
    
    /**
     * Get truly online users - DIPERBARUI
     */
    public function getOnlineUsers()
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error' => 'Access denied'
            ]);
        }
        
        $minutes = $this->request->getGet('minutes') ?? 2;
        $users = $this->trackingModel->getOnlineUsers($minutes);
        
        return $this->response->setJSON([
            'success' => true,
            'count' => count($users),
            'users' => $users,
            'last_updated' => date('Y-m-d H:i:s')
        ]);
    }
    
  
   /**
 * Get live locations (API endpoint) - DIPERBARUI dengan debug
 */
public function getLiveLocations()
{
    // Set JSON header
    header('Content-Type: application/json');
    
    try {
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        if ($role === 'admin') {
            // Query sederhana untuk menghindari masalah join
            $db = \Config\Database::connect();
            
            // Ambil data terbaru per user (dalam 5 menit)
            $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            
            $query = $db->query("
                SELECT 
                    lt1.*,
                    u.nama,
                    u.nip,
                    u.role,
                    u.unit_kerja
                FROM location_tracking lt1
                INNER JOIN (
                    SELECT user_id, MAX(timestamp) as max_timestamp
                    FROM location_tracking
                    WHERE timestamp >= ?
                    AND is_online = 1
                    GROUP BY user_id
                ) lt2 ON lt1.user_id = lt2.user_id AND lt1.timestamp = lt2.max_timestamp
                INNER JOIN users u ON u.id = lt1.user_id
                WHERE u.is_active = 1
                AND lt1.latitude IS NOT NULL
                AND lt1.longitude IS NOT NULL
                ORDER BY lt1.timestamp DESC
            ", [$fiveMinutesAgo]);
            
            $locations = $query->getResultArray();
            
        } else {
            // Non-admin hanya lihat sendiri
            $locations = $this->trackingModel
                ->select('location_tracking.*, users.nama, users.nip, users.role, users.unit_kerja')
                ->join('users', 'users.id = location_tracking.user_id')
                ->where('users.is_active', 1)
                ->where('location_tracking.user_id', $userId)
                ->where('location_tracking.timestamp >=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
                ->where('location_tracking.latitude IS NOT NULL')
                ->where('location_tracking.longitude IS NOT NULL')
                ->orderBy('location_tracking.timestamp', 'DESC')
                ->findAll();
        }
        
        // Pastikan semua field ada
        $cleanLocations = [];
        foreach ($locations as $loc) {
            $cleanLocations[] = [
                'user_id' => $loc['user_id'] ?? 0,
                'nama' => $loc['nama'] ?? 'Unknown',
                'nip' => $loc['nip'] ?? '-',
                'unit_kerja' => $loc['unit_kerja'] ?? '-',
                'latitude' => $loc['latitude'] ?? 0,
                'longitude' => $loc['longitude'] ?? 0,
                'timestamp' => $loc['timestamp'] ?? date('Y-m-d H:i:s'),
                'last_seen' => $loc['last_seen'] ?? $loc['timestamp'] ?? date('Y-m-d H:i:s'),
                'status' => $loc['status'] ?? 'offline',
                'is_online' => $loc['is_online'] ?? 0
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $cleanLocations,
            'timestamp' => date('Y-m-d H:i:s'),
            'total' => count($cleanLocations)
        ]);
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Get live locations error: ' . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'error' => 'Failed to load locations',
            'data' => []
        ]);
        exit();
    }
}
    
    /**
     * Update location (API endpoint for mobile) - DIPERBARUI
     */
    public function getLatestLocations()
{
    // Subquery untuk mendapatkan record terbaru per user
    $subquery = $this->db->table($this->table)
        ->select('user_id, MAX(timestamp) as latest_timestamp')
        ->groupBy('user_id')
        ->getCompiledSelect();
    
    // Main query dengan join
    $query = $this->db->table("{$this->table} lt")
        ->select('lt.*, u.nama, u.nip, u.role, u.unit_kerja, 
                 CASE 
                     WHEN lt.last_seen >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) THEN "active"
                     WHEN lt.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN "idle"
                     ELSE "offline"
                 END as current_status')
        ->join("({$subquery}) latest", 'lt.user_id = latest.user_id AND lt.timestamp = latest.latest_timestamp')
        ->join('users u', 'u.id = lt.user_id')
        ->where('u.is_active', 1);
    
    // Debug query
    // log_message('debug', 'Latest locations query: ' . $query->getCompiledSelect());
    
    $result = $query->get()->getResultArray();
    
    // Debug result
    // log_message('debug', 'Latest locations result: ' . print_r($result, true));
    
    return $result;
}
    
    /**
     * Cleanup offline users (call via cron job)
     */
    public function cleanup()
    {
        // Proteksi dengan API key atau IP whitelist untuk cron job
        $apiKey = $this->request->getGet('key');
        $validKey = getenv('CRON_API_KEY') ?: 'your-cron-secret-key';
        
        if ($apiKey !== $validKey) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
        }
        
        $minutes = $this->request->getGet('minutes') ?? 5;
        $cleaned = $this->trackingModel->cleanupOfflineUsers($minutes);
        
        log_message('info', "Cleanup: Marked {$cleaned} users as offline");
        
        return $this->response->setJSON([
            'success' => true,
            'cleaned' => $cleaned,
            'message' => "Marked {$cleaned} users as offline",
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get user tracking statistics
     */
    public function getStats($userId = null)
    {
        if ($this->session->get('role') !== 'admin' && $userId !== $this->session->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error' => 'Access denied'
            ]);
        }
        
        if ($userId === null) {
            $userId = $this->session->get('user_id');
        }
        
        // Hitung stats
        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        $todayEnd = $today . ' 23:59:59';
        
        $totalLocations = $this->trackingModel
            ->where('user_id', $userId)
            ->countAllResults();
        
        $todayLocations = $this->trackingModel
            ->where('user_id', $userId)
            ->where('timestamp >=', $todayStart)
            ->where('timestamp <=', $todayEnd)
            ->countAllResults();
        
        $lastLocation = $this->trackingModel
            ->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        $status = $this->trackingModel->getUserStatus($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'stats' => [
                'total_locations' => $totalLocations,
                'today_locations' => $todayLocations,
                'current_status' => $status['status'],
                'is_online' => $status['is_online'],
                'last_seen' => $status['last_seen'],
                'last_location' => $lastLocation
            ]
        ]);
    }
    
    /**
     * Admin tracking management - DIPERBARUI
     */
    public function adminIndex()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('tracking'))
                ->with('error', 'Access denied. Admin only.');
        }
        
        // Get truly online users
        $onlineUsers = $this->trackingModel->getOnlineUsers(2);
        $totalUsers = $this->userModel->where('is_active', 1)->countAllResults();
        
        // Get recent tracking activity dengan status
        $recentActivity = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, 
                     CASE 
                         WHEN location_tracking.last_seen >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) THEN "active"
                         WHEN location_tracking.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN "idle"
                         ELSE "offline"
                     END as current_status')
            ->join('users', 'users.id = location_tracking.user_id')
            ->orderBy('location_tracking.timestamp', 'DESC')
            ->limit(20)
            ->findAll();
        
        // Get summary stats
        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        
        $todayUpdates = $this->trackingModel
            ->where('timestamp >=', $todayStart)
            ->countAllResults();
        
        $data = [
            'title' => 'Tracking Management - Admin',
            'total_users' => $totalUsers,
            'online_users' => count($onlineUsers),
            'online_users_list' => $onlineUsers,
            'today_updates' => $todayUpdates,
            'recent_activity' => $recentActivity,
            'user_role' => 'admin'
        ];
        
        return view('tracking/admin', $data);
    }
    
    /**
     * Auto cleanup untuk testing (jangan dipakai di production)
     */
    public function forceCleanup()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('tracking'))
                ->with('error', 'Access denied. Admin only.');
        }
        
        $minutes = $this->request->getGet('minutes') ?? 5;
        $cleaned = $this->trackingModel->cleanupOfflineUsers($minutes);
        
        return redirect()->to(base_url('tracking/admin'))
            ->with('success', "Berhasil membersihkan {$cleaned} user yang offline");
    }
    
    // ==================== METHOD LAMA YANG TETAP DIPAKAI ====================
    
    public function autoTrack($userId, $token = null)
    {
        // Validate token if provided
        if ($token) {
            $isValid = $this->validateTrackingToken($userId, $token);
            if (!$isValid) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'error' => 'Invalid token'
                ]);
            }
        }
        
        // Get user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error' => 'User not found'
            ]);
        }
        
        // Get approximate location based on user data
        $location = $this->getUserApproximateLocation($user);
        
        // Save to tracking dengan status
        $data = [
            'user_id' => $userId,
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'accuracy' => $location['accuracy'],
            'address' => $user['lokasi_pln'] . ' - ' . $user['unit_kerja'] . ' (Auto)',
            'device_info' => 'Auto-Track System',
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'is_online' => 1,
            'last_seen' => date('Y-m-d H:i:s')
        ];
        
        $this->trackingModel->insert($data);
        
        // Update last_tracked
        $this->userModel->update($userId, ['last_tracked' => date('Y-m-d H:i:s')]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Location auto-tracked',
            'location' => $location,
            'user' => [
                'nama' => $user['nama'],
                'nip' => $user['nip'],
                'unit_kerja' => $user['unit_kerja']
            ]
        ]);
    }
    
    private function getUserApproximateLocation($user)
    {
        // Base locations for PLN offices
        $plnLocations = [
            'PLN Pusat Jakarta' => ['lat' => -6.2088, 'lng' => 106.8456, 'accuracy' => 500],
            'PLN Bandung' => ['lat' => -6.9147, 'lng' => 107.6098, 'accuracy' => 500],
            'PLN Surabaya' => ['lat' => -7.2504, 'lng' => 112.7688, 'accuracy' => 500],
            'PLN Bogor' => ['lat' => -6.5971, 'lng' => 106.8060, 'accuracy' => 500],
            'PLN Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319, 'accuracy' => 500],
            'PLN Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756, 'accuracy' => 500],
        ];
        
        $default = $plnLocations[$user['lokasi_pln']] ?? 
                   ['lat' => -6.2088, 'lng' => 106.8456, 'accuracy' => 1000];
        
        // Add small random variation (100-500 meters)
        $default['lat'] += (rand(-5, 5) / 10000);
        $default['lng'] += (rand(-5, 5) / 10000);
        
        return $default;
    }
    
    /**
     * Leaflet map view
     */
    public function leaflet()
    {
        $role = $this->session->get('role');
        $userId = $this->session->get('user_id');
        
        $data = [
            'title' => 'Live Tracking Map - Leaflet',
            'user_role' => $role,
            'user_nama' => $this->session->get('nama'),
            'default_lat' => -6.2088,
            'default_lng' => 106.8456
        ];
        
        return view('tracking/leaflet', $data);
    }
    
    /**
     * Google Maps view (if you want to use Google Maps later)
     */
    public function map()
    {
        $data = [
            'title' => 'Live Tracking Map - Google',
            'user_role' => $this->session->get('role'),
            'user_nama' => $this->session->get('nama'),
            'google_maps_api_key' => getenv('GOOGLE_MAPS_API_KEY') ?: '',
            'default_lat' => -6.2088,
            'default_lng' => 106.8456
        ];
        
        return view('tracking/map', $data);
    }
    
    /**
     * Mobile tracker page
     */
    public function mobile()
    {
        $userId = $this->session->get('user_id');
        
        // Generate or get tracking token
        $user = $this->userModel->find($userId);
        if (empty($user['tracking_token'])) {
            $token = md5($user['nip'] . time() . rand(1000, 9999));
            $this->userModel->update($userId, ['tracking_token' => $token]);
        } else {
            $token = $user['tracking_token'];
        }
        
        $data = [
            'title' => 'PLN Mobile Tracker',
            'user_id' => $userId,
            'user_nama' => $this->session->get('nama'),
            'tracking_token' => $token,
            'session_id' => session_id() // Tambahkan session ID
        ];
        
        return view('tracking/mobile', $data);
    }
    
    public function updateLocation()
{
    // Set JSON header
    header('Content-Type: application/json');
    
    // Get input
    $input = $this->request->getJSON(true);
    if (!$input) {
        $input = $this->request->getPost();
    }
    
    // Log untuk debug
    log_message('debug', 'API Update Location: ' . print_r($input, true));
    
    // Validate required fields
    if (empty($input['user_id']) || empty($input['latitude']) || empty($input['longitude'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: user_id, latitude, longitude'
        ]);
        exit();
    }
    
    $userId = $input['user_id'];
    $token = $input['token'] ?? null;
    
    // Validate token
    if ($token) {
        $isValid = $this->validateTrackingToken($userId, $token);
        if (!$isValid) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid tracking token'
            ]);
            exit();
        }
    }
    
    try {
        // Gunakan Database Query Builder langsung
        $db = \Config\Database::connect();
        $builder = $db->table('location_tracking');
        
        // Data untuk insert
        $locationData = [
            'user_id' => $userId,
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'accuracy' => $input['accuracy'] ?? null,
            'altitude' => $input['altitude'] ?? null,
            'speed' => $input['speed'] ?? null,
            'heading' => $input['heading'] ?? null,
            'address' => $input['address'] ?? null,
            'battery_level' => $input['battery_level'] ?? null,
            'is_charging' => isset($input['is_charging']) ? ($input['is_charging'] ? 1 : 0) : 0,
            'device_info' => $input['device_info'] ?? null,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'is_online' => 1,
            'last_seen' => date('Y-m-d H:i:s'),
            'session_id' => $input['session_id'] ?? 'mobile_' . $userId
        ];
        
        log_message('debug', 'Inserting location data: ' . print_r($locationData, true));
        
        // Insert data baru
        $inserted = $builder->insert($locationData);
        
        if ($inserted) {
            // Update last_tracked in users table
            $userBuilder = $db->table('users');
            $userBuilder->where('id', $userId)
                       ->update(['last_tracked' => date('Y-m-d H:i:s')]);
            
            log_message('debug', 'Location saved successfully for user: ' . $userId);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Location updated successfully',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            log_message('error', 'Failed to insert location for user: ' . $userId);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save location'
            ]);
        }
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Update location error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ]);
        exit();
    }
}

    /**
     * Update battery status
     */
    public function updateBattery()
    {
        $input = $this->request->getJSON(true);
        if (!$input) {
            $input = $this->request->getPost();
        }
        
        if (empty($input['user_id']) || !isset($input['battery_level'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Missing required fields'
            ]);
        }
        
        // Update latest location with battery info
        $latestLocation = $this->trackingModel
            ->where('user_id', $input['user_id'])
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        if ($latestLocation) {
            $this->trackingModel->update($latestLocation['id'], [
                'battery_level' => $input['battery_level'],
                'is_charging' => isset($input['is_charging']) ? ($input['is_charging'] ? 1 : 0) : 0,
                'last_seen' => date('Y-m-d H:i:s') // Update last seen juga
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Battery status updated'
        ]);
    }
    
    /**
     * Location history
     */
    public function history($userId = null)
    {
        $role = $this->session->get('role');
        $currentUserId = $this->session->get('user_id');
        
        // Admin can view any user's history, regular users only their own
        if ($role !== 'admin') {
            $userId = $currentUserId;
        } elseif ($userId === null) {
            $userId = $currentUserId;
        }
        
        // Get user info
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to(base_url('tracking'))
                ->with('error', 'User not found');
        }
        
        // Get location history dengan status
        $locations = $this->trackingModel
            ->select('location_tracking.*, 
                     CASE 
                         WHEN last_seen >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) THEN "active"
                         WHEN last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN "idle"
                         ELSE "offline"
                     END as current_status')
            ->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->paginate(20);
        
        $data = [
            'title' => 'Location History - ' . $user['nama'],
            'locations' => $locations,
            'user' => $user,
            'user_role' => $role,
            'pager' => $this->trackingModel->pager
        ];
        
        return view('tracking/history', $data);
    }
    
    /**
     * Export tracking data
     */
    public function exportAll()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('tracking'))
                ->with('error', 'Access denied. Admin only.');
        }
        
        $data = $this->trackingModel
            ->select('location_tracking.*, users.nama, users.nip, users.unit_kerja')
            ->join('users', 'users.id = location_tracking.user_id')
            ->orderBy('location_tracking.timestamp', 'DESC')
            ->findAll();
        
        $filename = 'tracking_export_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'No', 'Timestamp', 'Last Seen', 'Status', 'Online', 'Nama', 'NIP', 'Unit Kerja',
            'Latitude', 'Longitude', 'Accuracy', 'Address',
            'Battery', 'Device', 'Speed'
        ]);
        
        // Data
        $no = 1;
        foreach ($data as $row) {
            fputcsv($output, [
                $no++,
                $row['timestamp'],
                $row['last_seen'] ?? 'N/A',
                $row['status'] ?? 'offline',
                $row['is_online'] ?? 0,
                $row['nama'],
                $row['nip'],
                $row['unit_kerja'],
                $row['latitude'],
                $row['longitude'],
                $row['accuracy'] ?? 'N/A',
                substr($row['address'] ?? 'N/A', 0, 50),
                $row['battery_level'] ?? 'N/A',
                $row['device_info'] ?? 'N/A',
                $row['speed'] ?? 'N/A'
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Start tracking session
     */
    public function startTracking()
    {
        $userId = $this->session->get('user_id');
        
        // Generate new tracking token
        $token = md5($userId . time() . rand(1000, 9999));
        $this->userModel->update($userId, [
            'tracking_token' => $token,
            'last_tracked' => date('Y-m-d H:i:s')
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'token' => $token,
            'message' => 'Tracking started'
        ]);
    }
    
    /**
     * Stop tracking session
     */
    public function stopTracking()
    {
        $userId = $this->session->get('user_id');
        
        // Clear tracking token
        $this->userModel->update($userId, [
            'tracking_token' => null
        ]);
        
        // Juga mark as offline
        $this->trackingModel->markOffline($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Tracking stopped'
        ]);
    }
    
    /**
     * Get tracking status
     */
    public function trackingStatus()
    {
        $userId = $this->session->get('user_id');
        
        $user = $this->userModel->find($userId);
        $isTracking = !empty($user['tracking_token']);
        
        $status = $this->trackingModel->getUserStatus($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'is_tracking' => $isTracking,
            'current_status' => $status['status'],
            'is_online' => $status['is_online'],
            'last_seen' => $status['last_seen'],
            'tracking_token' => $user['tracking_token'] ?? null
        ]);
    }
    
    /**
     * Private helper method to validate tracking token
     */
    private function validateTrackingToken($userId, $token)
    {
        $user = $this->userModel->find($userId);
        return $user && $user['tracking_token'] === $token;
    }
}