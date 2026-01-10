<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes (tanpa authentication)
$routes->get('/', 'Auth::login'); // Redirect root ke login
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::processLogin');
$routes->get('auth/logout', 'Auth::logout');

// Auth routes (hanya untuk admin)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // KWH Calculator
    $routes->get('kwh', 'Kwh::index');
    $routes->post('kwh/save', 'Kwh::save');
    $routes->get('kwh/export', 'Kwh::export');
    $routes->get('kwh/delete/(:num)', 'Kwh::delete/$1');
    $routes->get('kwh/clearAll', 'Kwh::clearAll');
    
    // Export PDF
    $routes->get('export/pdf', 'Export::pdf');
    $routes->get('export/pdf/(:num)', 'Export::pdf/$1');
    
    // Profile
    $routes->get('auth/profile', 'Auth::profile');
    $routes->post('auth/profile', 'Auth::updateProfile');
});

// Admin only routes
$routes->group('', ['filter' => 'auth:admin'], function($routes) {
    // User Management
    $routes->get('auth/register', 'Auth::register');
    $routes->post('auth/register', 'Auth::processRegister');
    $routes->get('users', 'Users::index');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->get('users/delete/(:num)', 'Users::delete/$1');
});

// Public access untuk view photo (tanpa auth)
$routes->get('kwh/viewPhoto/(:any)', 'Kwh::viewPhoto/$1');