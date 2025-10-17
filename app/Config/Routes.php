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

// Student announcements — must be accessible to all logged-in students
$routes->get('/announcements', 'Announcement::index');

// Protected routes — apply RoleAuth filter
$routes->group('admin', ['filter' => 'roleauth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    // Add more admin-only routes here
});

$routes->group('teacher', ['filter' => 'roleauth'], function ($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    // Add more teacher-only routes here
});

$routes->group('student', ['filter' => 'roleauth'], function ($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    // Add more student-only routes here
});

