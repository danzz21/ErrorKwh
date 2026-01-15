<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }
    }
    
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'User not found');
        }
        
        $data = [
            'title' => 'Settings - PLN',
            'user_role' => session()->get('role'),
            'user_nama' => session()->get('nama'),
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];
        
        return view('settings/index', $data);
    }
    
    public function update()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'User not found');
        }
        
        $validation = \Config\Services::validation();
        
        // Validation rules
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'current_password' => 'permit_empty',
            'new_password' => 'permit_empty|min_length[6]',
            'confirm_password' => 'matches[new_password]'
        ];
        
        if (!$validation->setRules($rules)->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Prepare update data
        $updateData = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'lokasi_pln' => $this->request->getPost('lokasi_pln')
        ];
        
        // Handle password change
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        
        if (!empty($currentPassword) && !empty($newPassword)) {
            // Verify current password
            if (password_verify($currentPassword, $user['password'])) {
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Current password is incorrect');
            }
        }
        
        // Update user
        if ($this->userModel->update($userId, $updateData)) {
            // Update session data
            session()->set([
                'nama' => $updateData['nama'],
                'email' => $updateData['email']
            ]);
            
            return redirect()->to(base_url('settings'))
                ->with('success', 'Settings updated successfully');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update settings');
        }
    }
    
    public function notifications()
    {
        $data = [
            'title' => 'Notification Settings - PLN',
            'user_role' => session()->get('role'),
            'user_nama' => session()->get('nama')
        ];
        
        return view('settings/notifications', $data);
    }
    
    public function appearance()
    {
        $data = [
            'title' => 'Appearance Settings - PLN',
            'user_role' => session()->get('role'),
            'user_nama' => session()->get('nama')
        ];
        
        return view('settings/appearance', $data);
    }
}