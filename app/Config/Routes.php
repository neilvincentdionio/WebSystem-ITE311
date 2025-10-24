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

// Course Enrollment (AJAX)
$routes->get('course/enroll', 'Course::enroll');
$routes->post('/course/enroll', 'Course::enroll');

// Teacher Routes (Protected by roleauth filter)
$routes->group('teacher', ['filter' => 'roleauth:teacher'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});


// Admin Routes (Protected by roleauth filter)
$routes->group('admin', ['filter' => 'roleauth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    // Add more admin-only routes here
});

 $routes->group('student', ['filter' => 'roleauth:student'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    // Add more student-only routes here
});
