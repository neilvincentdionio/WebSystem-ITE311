<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

/// Authentication
$routes->group('auth', function ($routes) {
    // Register routes - both GET and POST handled by same method
    $routes->get('register', 'Auth::register');         // Show register form
    $routes->post('register', 'Auth::register');        // Handle register submission
    
    // Login routes - both GET and POST handled by same method
    $routes->get('login', 'Auth::login');               // Show login form
    $routes->post('login', 'Auth::login');              // Handle login submission

    $routes->get('logout', 'Auth::logout');    
});

// Dashboard
$routes->get('dashboard', 'Auth::dashboard');