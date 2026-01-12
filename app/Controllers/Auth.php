<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
{
    $this->userModel = new UserModel();
    $this->session = \Config\Services::session();
    helper(['form', 'url']);
    
    // Cek apakah sudah ada admin di database
    $this->checkFirstTimeSetup();
}

private function checkFirstTimeSetup()
{
    // Cek apakah ada user di database
    $userCount = $this->userModel->countAll();
    
    // Jika belum ada user sama sekali, buat admin default
    if ($userCount === 0) {
        $this->createDefaultAdmin();
    }
}

private function createDefaultAdmin()
{
    // Generate hash untuk password 'admin123'
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $data = [
        'nip' => 'ADMIN001',
        'nama' => 'Administrator System',
        'email' => 'admin@pln.co.id',
        'password' => $password_hash, // SIMPAN HASH, BUKAN PLAIN TEXT
        'role' => 'admin',
        'unit_kerja' => 'IT Department',
        'lokasi_pln' => 'PLN Pusat Jakarta',
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Save langsung ke database tanpa melalui Model (untuk hindari double hash)
    $db = \Config\Database::connect();
    $db->table('users')->insert($data);
    
    log_message('info', 'Default admin user created: ADMIN001/admin123');
    log_message('debug', 'Admin password hash: ' . $password_hash);
}
    
    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        
        $data = [
            'title' => 'Login - PLN KWH Calculator',
            'validation' => \Config\Services::validation()
        ];
        
        return view('auth/login', $data);
    }
    
  public function processLogin()
{
    log_message('debug', '=== LOGIN ATTEMPT START ===');
    
    $validation = \Config\Services::validation();
    $validation->setRules([
        'nip' => 'required',
        'password' => 'required'
    ]);
    
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        log_message('error', 'Validation failed: ' . print_r($errors, true));
        return redirect()->back()
            ->withInput()
            ->with('error', implode(', ', $errors));
    }
    
    $nip = $this->request->getPost('nip');
    $password = $this->request->getPost('password');
    
    log_message('debug', "Login attempt - NIP: {$nip}, Password: {$password}");
    
    // Cari user by NIP atau Email
    $user = $this->userModel->getByNIP($nip);
    
    if (!$user) {
        // Coba cari by email
        $user = $this->userModel->getByEmail($nip);
    }
    
    if (!$user) {
        log_message('error', "User not found: {$nip}");
        return redirect()->back()
            ->withInput()
            ->with('error', 'NIP/Email tidak ditemukan');
    }
    
    log_message('debug', "User found - ID: {$user['id']}, NIP: {$user['nip']}, Nama: {$user['nama']}");
    log_message('debug', "User role: {$user['role']}, Active: {$user['is_active']}");
    log_message('debug', "Password hash stored: " . substr($user['password'], 0, 30) . "...");
    
    if (!$user['is_active']) {
        log_message('error', "User inactive: {$nip}");
        return redirect()->back()
            ->withInput()
            ->with('error', 'Akun dinonaktifkan. Hubungi administrator.');
    }
    
    // VERIFIKASI PASSWORD - PERBAIKAN UTAMA
    log_message('debug', "Verifying password...");
    log_message('debug', "Input password length: " . strlen($password));
    log_message('debug', "Stored hash length: " . strlen($user['password']));
    
    // Coba verifikasi dengan password_verify langsung
    $passwordValid = password_verify($password, $user['password']);
    
    if ($passwordValid) {
        log_message('debug', "✅ Password verification SUCCESS!");
    } else {
        log_message('error', "❌ Password verification FAILED!");
        
        // DEBUG: Coba semua kemungkinan password
        $testPasswords = [
            'admin123', 'admin', '123456', 'password', 
            'Admin123', 'ADMIN123', 'admin@123', 'admin123!'
        ];
        
        foreach ($testPasswords as $testPwd) {
            $testResult = password_verify($testPwd, $user['password']);
            if ($testResult) {
                log_message('debug', "⚠️ Password might be: '{$testPwd}'");
            }
        }
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Password salah. Hubungi admin untuk reset password.');
    }
    
    // Set session data
    $sessionData = [
        'user_id' => $user['id'],
        'nip' => $user['nip'],
        'nama' => $user['nama'],
        'email' => $user['email'],
        'role' => $user['role'],
        'unit_kerja' => $user['unit_kerja'],
        'lokasi_pln' => $user['lokasi_pln'],
        'foto' => $user['foto'] ?? null,
        'isLoggedIn' => true
    ];
    
    $this->session->set($sessionData);
    
    // Update last login
    $this->userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
    
    log_message('info', "✅ User logged in: {$user['nip']} - {$user['nama']}");
    
    // Redirect berdasarkan role
    return redirect()->to(base_url('dashboard'))
        ->with('success', 'Selamat datang, ' . $user['nama']);
}
    
    public function register()
    {
        // Hanya admin yang bisa register user baru
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Hanya administrator yang dapat menambah user');
        }
        
        $data = [
            'title' => 'Register User Baru - PLN',
            'validation' => \Config\Services::validation()
        ];
        
        return view('auth/register', $data);
    }
    
    public function processRegister()
    {
        // Hanya admin
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('auth/login'));
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nip' => 'required|min_length[5]|is_unique[users.nip]',
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'role' => 'required|in_list[admin,pln_metrologi,pln_teknis,pln_operasional]',
            'unit_kerja' => 'required',
            'lokasi_pln' => 'required'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nip' => $this->request->getPost('nip'),
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'lokasi_pln' => $this->request->getPost('lokasi_pln'),
            'telepon' => $this->request->getPost('telepon'),
            'is_active' => true
        ];
        
        if ($this->userModel->save($data)) {
            return redirect()->to(base_url('users'))
                ->with('success', 'User berhasil ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user');
        }
    }
    private function autoTrackUserLocation($userId)
{
    try {
        // Include tracking model
        $trackingModel = new \App\Models\LocationTrackingModel();
        $userModel = new \App\Models\UserModel();
        
        // Coba dapatkan lokasi dari IP address atau browser
        $ipAddress = $this->request->getIPAddress();
        
        // Data default lokasi berdasarkan lokasi_pln di database
        $user = $userModel->find($userId);
        $defaultLocation = $this->getDefaultLocation($user['lokasi_pln']);
        
        // Coba dapatkan lokasi dari browser (jika user mengizinkan)
        $data = [
            'user_id' => $userId,
            'latitude' => $defaultLocation['lat'],
            'longitude' => $defaultLocation['lng'],
            'accuracy' => 5000, // Accuracy rendah karena dari IP
            'address' => $user['lokasi_pln'] . ' (Auto-track saat login)',
            'device_info' => $this->request->getUserAgent()->getBrowser() . ' - ' . 
                           $this->request->getUserAgent()->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Simpan ke tracking database
        $trackingModel->insert($data);
        
        log_message('info', 'Auto-tracking location for user ID: ' . $userId);
        
    } catch (\Exception $e) {
        log_message('error', 'Auto-tracking failed: ' . $e->getMessage());
    }
}

private function getDefaultLocation($lokasiPln)
{
    // Mapping lokasi PLN ke koordinat
    $locations = [
        'PLN Pusat Jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
        'PLN Bandung' => ['lat' => -6.9147, 'lng' => 107.6098],
        'PLN Surabaya' => ['lat' => -7.2504, 'lng' => 112.7688],
        'PLN Bogor' => ['lat' => -6.5971, 'lng' => 106.8060],
        'PLN Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319],
        'PLN Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756],
        'PLN Semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
        'PLN Yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
    ];
    
    return $locations[$lokasiPln] ?? ['lat' => -6.2088, 'lng' => 106.8456]; // Default Jakarta
}
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Anda telah logout');
    }
    
    public function profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
        
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        
        $data = [
            'title' => 'Profile - ' . $user['nama'],
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];
        
        return view('auth/profile', $data);
    }
    
    public function updateProfile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
        
        $userId = $this->session->get('user_id');
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'telepon' => 'permit_empty|min_length[10]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'lokasi_pln' => $this->request->getPost('lokasi_pln')
        ];
        
        // Handle password change if provided
        $password = $this->request->getPost('password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        if (!empty($password)) {
            if ($password !== $confirm_password) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Password konfirmasi tidak cocok');
            }
            $data['password'] = $password;
        }
        
        // Handle photo upload
        $photo = $this->request->getFile('foto');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(WRITEPATH . 'uploads/users', $newName);
            $data['foto'] = $newName;
        }
        
        if ($this->userModel->update($userId, $data)) {
            // Update session data
            $user = $this->userModel->find($userId);
            $sessionData = [
                'nama' => $user['nama'],
                'email' => $user['email'],
                'unit_kerja' => $user['unit_kerja'],
                'lokasi_pln' => $user['lokasi_pln'],
                'foto' => $user['foto']
            ];
            $this->session->set($sessionData);
            
            return redirect()->to(base_url('auth/profile'))
                ->with('success', 'Profile berhasil diperbarui');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profile');
        }
    }
}