<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        
        // Only admin can access
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Akses ditolak. Hanya administrator.');
        }
    }
    
   public function index()
{
    $users = $this->userModel->orderBy('nama', 'ASC')->findAll();
    
    $data = [
        'title' => 'User Management - PLN',
        'users' => $users,
        'user_role' => session()->get('role'),        // TAMBAHKAN
        'user_nama' => session()->get('nama')         // TAMBAHKAN
    ];
    
    return view('users/index', $data);
}
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('users'))
                ->with('error', 'User tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit User - ' . $user['nama'],
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];
        
        return view('users/edit', $data);
    }
    
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('users'))
                ->with('error', 'User tidak ditemukan');
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,pln_metrologi,pln_teknis,pln_operasional]',
            'is_active' => 'required|in_list[0,1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'lokasi_pln' => $this->request->getPost('lokasi_pln'),
            'telepon' => $this->request->getPost('telepon'),
            'is_active' => $this->request->getPost('is_active')
        ];
        
        // Handle password change if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        if ($this->userModel->update($id, $data)) {
            return redirect()->to(base_url('users'))
                ->with('success', 'User berhasil diperbarui');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user');
        }
    }
    
    public function delete($id)
    {
        // Prevent self-deletion
        if ($id == session()->get('user_id')) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        if ($this->userModel->delete($id)) {
            return redirect()->to(base_url('users'))
                ->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to(base_url('users'))
                ->with('error', 'Gagal menghapus user');
        }
    }
}