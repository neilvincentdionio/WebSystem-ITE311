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
$routes->get('login', 'Auth::login');        // Show login form
$routes->post('login', 'Auth::login');       // Process login
$routes->get('register', 'Auth::register');  // Show register form
$routes->post('register', 'Auth::register'); // Process registration
$routes->get('logout', 'Auth::logout');


});

// Generic Dashboard (fallback)
$routes->get('dashboard', 'Auth::dashboard');

// Role-based Dashboard Routes using separate controllers
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('teacher/dashboard', 'Teacher::dashboard');
$routes->get('student/dashboard', 'Student::dashboard');

$routes->group('admin', function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('users', 'Admin::manageUsers');   
    $routes->get('courses', 'Admin::manageCourses');
    $routes->get('logout', 'Admin::logout');
});

$routes->group('teacher', function ($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    $routes->get('create-course', 'Teacher::createCourse');
    $routes->get('logout', 'Teacher::logout');
});

$routes->group('student', function ($routes) {
    $routes->get('dashboard', 'Student::dashboard');
});

