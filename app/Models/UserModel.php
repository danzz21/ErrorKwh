<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nip', 'nama', 'email', 'password', 'role',
        'unit_kerja', 'lokasi_pln', 'telepon', 'foto',
        'is_active', 'last_login'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'nip' => 'required|min_length[5]|is_unique[users.nip]',
        'nama' => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,pln_metrologi,pln_teknis,pln_operasional]'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
   protected function hashPassword(array $data)
{
    if (isset($data['data']['password'])) {
        $password = $data['data']['password'];
        
        // Cek jika password sudah di-hash (bcrypt format)
        // Regex yang lebih akurat untuk bcrypt
        if (preg_match('/^\$2[ay]\$\d{2}\$[A-Za-z0-9\.\/]{53}$/', $password)) {
            log_message('debug', 'Password already hashed, skipping re-hash');
            return $data;
        }
        
        // Hanya hash jika panjang < 60 karakter (hash bcrypt minimal 60 chars)
        if (strlen($password) < 60) {
            $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
            log_message('debug', 'Password hashed successfully');
        } else {
            log_message('debug', 'Password appears to be already hashed, length: ' . strlen($password));
        }
    }
    return $data;
}

// Tambah method untuk cek hash
public function isPasswordHashed($password)
{
    return preg_match('/^\$2[ayb]\$.{56}$/', $password);
}
    
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    public function getByNIP($nip)
    {
        return $this->where('nip', $nip)->first();
    }
    
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
    
    public function getUsersByRole($role = null)
    {
        if ($role) {
            return $this->where('role', $role)->findAll();
        }
        return $this->findAll();
    }
}