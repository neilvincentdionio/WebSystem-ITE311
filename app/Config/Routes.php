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

// Course Search
$routes->get('course/search', 'Course::search');
$routes->post('course/search', 'Course::search');

// Courses listing and search
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');
$routes->get('/courses', 'Course::index');
$routes->get('/courses/index', 'Course::index');

// Teacher Routes (Protected by roleauth filter)
$routes->group('teacher', ['filter' => 'roleauth:teacher'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Admin Routes (Protected by roleauth filter)
$routes->group('admin', ['filter' => 'roleauth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('courses', 'Admin::courses');
     // Add more admin-only routes here
});

$routes->group('student', ['filter' => 'roleauth:student'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    // Add more student-only routes here
});

// Materials management and access
$routes->get('/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// Optional: Course materials listing for enrolled students
$routes->get('/course/(:num)/materials', 'Materials::listing/$1');

// Notifications API
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
