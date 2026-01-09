<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Kwh::index');
$routes->post('kwh/save', 'Kwh::save');
$routes->get('kwh/viewPhoto/(:any)', 'Kwh::viewPhoto/$1');
$routes->get('kwh/export', 'Kwh::export');
$routes->get('kwh/delete/(:num)', 'Kwh::delete/$1');
$routes->get('kwh/clearAll', 'Kwh::clearAll');

// Export PDF routes (simple)
$routes->get('export/pdf', 'Export::pdf'); // All data
$routes->get('export/pdf/(:num)', 'Export::pdf/$1'); // Single data