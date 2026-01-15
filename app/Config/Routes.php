<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes (tanpa authentication)
$routes->get('/', 'Auth::login');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::processLogin');
$routes->get('auth/logout', 'Auth::logout');

// ========== TRACKING API (PUBLIC - NO AUTH NEEDED) ==========
$routes->post('tracking-api/update', 'Tracking::updateLocation');
$routes->post('tracking-api/heartbeat', 'Tracking::heartbeat');
$routes->post('tracking-api/mark-offline', 'Tracking::markOffline');

// Auth routes (hanya untuk yang login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/stats', 'Dashboard::getStats');
    $routes->get('settings', 'Settings::index');
$routes->post('settings/update', 'Settings::update');
    
    // KWH Calculator - TAMBAHKAN ROUTES BARU INI
    $routes->get('kwh', 'Kwh::index');
    $routes->get('kwh/all', 'Kwh::all');                    // NEW: View all data
    $routes->get('kwh/view/(:num)', 'Kwh::view/$1');        // NEW: View detail by ID
    $routes->get('kwh/export', 'Kwh::export');
    $routes->get('kwh/export/all', 'Kwh::export');         // NEW: Alias untuk export all
    $routes->post('kwh/save', 'Kwh::save');
    $routes->get('kwh/delete/(:num)', 'Kwh::delete/$1');
    $routes->get('kwh/clearAll', 'Kwh::clearAll');
    $routes->get('kwh/switchMode', 'Kwh::switchMode');
    
    // Export PDF
    $routes->get('export/pdf', 'Export::pdf');
    $routes->get('export/pdf/(:num)', 'Export::pdf/$1');
     $routes->get('export/all', 'Kwh::export');
    
     $routes->group('reports', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Reports::index');
    $routes->get('kwh', 'Reports::kwh');
    $routes->get('tracking', 'Reports::tracking');
    $routes->get('export', 'Reports::export');
});
    // Profile
    $routes->get('auth/profile', 'Auth::profile');
    $routes->post('auth/profile', 'Auth::updateProfile');
    
    // ========== TRACKING WEB ==========
    $routes->get('tracking', 'Tracking::index');
    $routes->get('tracking/leaflet', 'Tracking::leaflet');
    $routes->get('tracking/map', 'Tracking::map');
    $routes->get('tracking/mobile', 'Tracking::mobile');
    $routes->get('tracking/mobile-tracker', 'Tracking::mobileTracker');
    $routes->get('tracking/history', 'Tracking::history');
    $routes->get('tracking/history/(:num)', 'Tracking::history/$1');  // FIXED: This exists
    $routes->get('tracking/getLiveLocations', 'Tracking::getLiveLocations');
    $routes->get('tracking/start', 'Tracking::startTracking');
    $routes->get('tracking/stop', 'Tracking::stopTracking');
    $routes->get('tracking/status', 'Tracking::trackingStatus');
    $routes->get('tracking/stats', 'Tracking::getStats');
    $routes->get('tracking/stats/(:num)', 'Tracking::getStats/$1');
    $routes->get('tracking/checkStatus', 'Tracking::checkStatus');
    $routes->get('tracking/checkStatus/(:num)', 'Tracking::checkStatus/$1');
    
    // Auto tracking
    $routes->get('tracking/auto/(:num)', 'Tracking::autoTrack/$1');
    $routes->get('tracking/auto/(:num)/(:any)', 'Tracking::autoTrack/$1/$2');
    
    // ========== USERS (UNIVERSAL ACCESS) ==========
    $routes->get('users', 'Users::index');  // Semua user bisa lihat tapi hanya admin bisa edit
});

// Tambahkan di dalam auth group:
$routes->group('settings', function($routes) {
    $routes->get('/', 'Settings::index');
    $routes->post('update', 'Settings::update');
    $routes->get('notifications', 'Settings::notifications');
    $routes->get('appearance', 'Settings::appearance');
});
// Admin only routes
$routes->group('', ['filter' => 'auth:admin'], function($routes) {
    // User Management
    $routes->get('auth/register', 'Auth::register');
    $routes->post('auth/register', 'Auth::processRegister');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->get('users/delete/(:num)', 'Users::delete/$1');
    
    // Admin Tracking
    $routes->get('tracking/admin', 'Tracking::adminIndex');
    $routes->get('tracking/admin-dashboard', 'Tracking::adminDashboard');
    $routes->get('tracking/export', 'Tracking::exportAll');
    $routes->get('tracking/force-cleanup', 'Tracking::forceCleanup');
    $routes->get('tracking/online-users', 'Tracking::getOnlineUsers');
    $routes->get('tracking/cleanup', 'Tracking::cleanup');
});

// Public access untuk view photo
$routes->get('kwh/viewPhoto/(:any)', 'Kwh::viewPhoto/$1');