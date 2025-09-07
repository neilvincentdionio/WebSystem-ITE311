<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication
$routes->group('auth', function ($routes) {
    $routes->get('register', 'Auth::register');        // Show register form
    $routes->post('register', 'Auth::registerPost');   // Handle register

    $routes->get('login', 'Auth::login');              // Show login form
    $routes->post('login', 'Auth::loginPost');         // Handle login

    $routes->get('logout', 'Auth::logout');            // Logout user
});

// Dashboard (protected by Auth filter)
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
