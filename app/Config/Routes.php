<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// $routes->get('/', 'Home::index');


$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('leads', 'Leads::index');
    $routes->get('leads/create', 'Leads::create');
    $routes->post('leads/store', 'Leads::store');

    $routes->get('leads/(:num)/edit', 'Leads::edit/$1');
    $routes->post('leads/(:num)/update', 'Leads::update/$1');
    $routes->post('leads/(:num)/delete', 'Leads::delete/$1');

    $routes->get('customers', 'Customers::index');
    $routes->get('customers/create', 'Customers::create');
    $routes->post('customers/store', 'Customers::store');
    $routes->get('customers/(:num)/edit', 'Customers::edit/$1');
    $routes->post('customers/(:num)/update', 'Customers::update/$1');
    $routes->post('customers/(:num)/delete', 'Customers::delete/$1');
});