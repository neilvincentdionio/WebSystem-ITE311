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
    $routes->get('login', 'Auth::login');        // Show login form
    $routes->post('login', 'Auth::login');       // Process login
    $routes->get('register', 'Auth::register');  // Show register form
    $routes->post('register', 'Auth::register'); // Process registration
    $routes->get('logout', 'Auth::logout');      // Logout
});

// Generic Dashboard (role-based) 
$routes->get('/dashboard', 'Auth::dashboard');

// Course Enrollment (AJAX)
$routes->get('course/enroll', 'Course::enroll');
$routes->post('/course/enroll', 'Course::enroll');


