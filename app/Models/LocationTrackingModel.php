<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationTrackingModel extends Model
{
    protected $table = 'location_tracking';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'altitude',
        'speed',
        'heading',
        'address',
        'battery_level',
        'is_charging',
        'device_info',
        'timestamp',
        'status',
        'is_online',
        'last_seen',
        'session_id'
        // JANGAN tambahkan created_at dan updated_at di sini
    ];
    
    // DISABLE TIMESTAMPS
    protected $useTimestamps = false;
    // protected $createdField = 'created_at';
    // protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'latitude' => 'required|decimal',
        'longitude' => 'required|decimal'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /**
     * Update location with status management
     */
    /**
 * Update location with status management
 */
public function updateLocationWithStatus($data)
{
    $userId = $data['user_id'];
    $now = date('Y-m-d H:i:s');
    
    // Data untuk INSERT (bukan update)
    $locationData = [
        'user_id' => $userId,
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'accuracy' => $data['accuracy'] ?? null,
        'timestamp' => $now,
        'status' => 'active',
        'is_online' => 1,
        'last_seen' => $now,
        'session_id' => $data['session_id'] ?? session_id()
    ];
    
    // Optional fields
    $optionalFields = ['altitude', 'speed', 'heading', 'address', 
                      'battery_level', 'is_charging', 'device_info'];
    
    foreach ($optionalFields as $field) {
        if (isset($data[$field])) {
            $locationData[$field] = $data[$field];
        }
    }
    
    // INSERT data baru (setiap lokasi baru = record baru)
    return $this->insert($locationData);
}
    
    /**
     * Get latest location for each user
     */
   public function getLatestLocations()
{
    log_message('debug', '=== getLatestLocations() called ===');
    
    // Subquery untuk mendapatkan record terbaru per user
    $subquery = $this->db->table($this->table)
        ->select('user_id, MAX(timestamp) as latest_timestamp')
        ->groupBy('user_id')
        ->getCompiledSelect();
    
    log_message('debug', 'Subquery: ' . $subquery);
    
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
        ->where('u.is_active', 1)
        ->where('lt.is_online', 1);
    
    $sql = $query->getCompiledSelect();
    log_message('debug', 'Main query: ' . $sql);
    
    $result = $query->get()->getResultArray();
    
    log_message('debug', 'Result count: ' . count($result));
    log_message('debug', 'Result data: ' . print_r($result, true));
    
    return $result;
}
    
    /**
     * Get locations within time range untuk map
     */
    public function getLiveLocationsForMap($minutes = 15)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->db->table("{$this->table} lt")
            ->select('lt.*, u.nama, u.nip, u.role, u.unit_kerja')
            ->join('users u', 'u.id = lt.user_id')
            ->where('u.is_active', 1)
            ->where('lt.timestamp >=', $threshold)
            ->where('lt.latitude IS NOT NULL')
            ->where('lt.longitude IS NOT NULL')
            ->groupBy('lt.user_id')
            ->orderBy('lt.timestamp', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Update user status (tanpa location update)
     */
    public function updateUserStatus($userId, $status = 'active', $isOnline = 1)
    {
        $now = date('Y-m-d H:i:s');
        
        // Cari record terbaru untuk user ini
        $latest = $this->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        if ($latest) {
            return $this->update($latest['id'], [
                'status' => $status,
                'is_online' => $isOnline,
                'last_seen' => $now
            ]);
        }
        
        return false;
    }
    
    /**
     * Get truly online users (updated within 2 minutes)
     */
    public function getOnlineUsers($minutes = 2)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->db->table($this->table . ' lt')
            ->select('DISTINCT(lt.user_id), u.nama, lt.status, lt.last_seen, lt.latitude, lt.longitude')
            ->join('users u', 'u.id = lt.user_id')
            ->where('lt.last_seen >=', $threshold)
            ->where('lt.is_online', 1)
            ->where('u.is_active', 1)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get user status
     */
    public function getUserStatus($userId)
    {
        $record = $this->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        if (!$record) {
            return [
                'status' => 'offline',
                'is_online' => 0,
                'last_seen' => null
            ];
        }
        
        // Hitung status berdasarkan last_seen
        $lastSeen = strtotime($record['last_seen']);
        $now = time();
        $diffMinutes = ($now - $lastSeen) / 60;
        
        if ($diffMinutes <= 2) {
            $status = 'active';
        } elseif ($diffMinutes <= 5) {
            $status = 'idle';
        } else {
            $status = 'offline';
        }
        
        return [
            'status' => $status,
            'is_online' => $record['is_online'],
            'last_seen' => $record['last_seen'],
            'location' => [
                'latitude' => $record['latitude'],
                'longitude' => $record['longitude']
            ]
        ];
    }
    
    /**
     * Cleanup offline users (call via cron job)
     */
    public function cleanupOfflineUsers($offlineMinutes = 5)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$offlineMinutes} minutes"));
        
        return $this->db->table($this->table)
            ->where('last_seen <', $threshold)
            ->where('is_online', 1)
            ->update([
                'status' => 'offline',
                'is_online' => 0
            ]);
    }
    
    /**
     * Mark user as offline
     */
    public function markOffline($userId)
    {
        $latest = $this->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        if ($latest) {
            return $this->update($latest['id'], [
                'status' => 'offline',
                'is_online' => 0,
                'last_seen' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false;
    }
    
    /**
     * Heartbeat update (untuk maintain connection)
     */
    public function updateHeartbeat($userId, $sessionId = null)
    {
        $now = date('Y-m-d H:i:s');
        
        $latest = $this->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->first();
        
        if ($latest) {
            $data = [
                'last_seen' => $now,
                'status' => 'active',
                'is_online' => 1
            ];
            
            if ($sessionId) {
                $data['session_id'] = $sessionId;
            }
            
            return $this->update($latest['id'], $data);
        }
        
        return false;
    }
}