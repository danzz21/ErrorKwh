<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika user belum login
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu');
            return redirect()->to(base_url('auth/login'));
        }
        
        // Jika ada role requirement
        if ($arguments) {
            $userRole = session()->get('role');
            
            // Check if user has required role
            if (!in_array($userRole, $arguments)) {
                session()->setFlashdata('error', 'Akses ditolak. Role tidak memenuhi.');
                return redirect()->to(base_url('dashboard'));
            }
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        return $response;
    }
}