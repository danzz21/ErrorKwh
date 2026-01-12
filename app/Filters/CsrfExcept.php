<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CsrfExcept implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip CSRF check untuk API endpoints
        // You can add specific path checks here if needed
        return $request;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}