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
$routes->get('/course/(:num)', 'Course::show/$1');

// Teacher Routes (Protected by roleauth filter)
$routes->group('teacher', ['filter' => 'roleauth:teacher'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    $routes->get('courses', 'Teacher::courses');
    $routes->get('students', 'Teacher::students');
    $routes->get('schedule', 'Teacher::schedule');
    
    // Assignment approval actions
    $routes->post('assignment/accept', 'Teacher::acceptAssignment');
    $routes->post('assignment/reject', 'Teacher::rejectAssignment');
    
    // Enrollment Request Management Routes
    $routes->get('enrollment-requests', 'Teacher::enrollmentRequests');
    $routes->get('enrollment-requests/(:any)', 'Teacher::enrollmentRequests/$1');
    $routes->post('enrollment/accept', 'Teacher::acceptEnrollment');
    $routes->post('enrollment/reject', 'Teacher::rejectEnrollment');
    $routes->post('enrollment/request', 'Teacher::createEnrollmentRequest');
    
    // Student Enrollment Routes
    $routes->post('enroll-student', 'Teacher::enrollStudent');
    
    // Grading System Routes
    $routes->get('grading/(:num)', 'Teacher::grading/$1');
    $routes->post('set-grading-weights', 'Teacher::setGradingWeights');
    $routes->get('student-grades/(:num)', 'Teacher::studentGrades/$1');
    $routes->get('student-grades/(:num)/(:num)', 'Teacher::studentGrades/$1/$2');
    $routes->post('save-grade', 'Teacher::saveGrade');
    $routes->post('save-bulk-grades', 'Teacher::saveBulkGrades');
    
    // Materials Management
    $routes->get('materials', 'Materials::index');
    
    // Enrollment Management Routes for Teachers
    $routes->get('enrollments', 'Admin::enrollments');
    $routes->get('enrollments/create/(:num)', 'Admin::enrollStudent/$1');
    $routes->post('enrollments/store', 'Admin::storeEnrollment');
    $routes->post('enrollments/remove/(:num)', 'Admin::removeEnrollment/$1');
    $routes->get('enrollments/course/(:num)', 'Admin::enrollStudent/$1');
});

// Student Routes (Protected by roleauth filter)
$routes->group('student', ['filter' => 'roleauth:student'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('enrollment-requests', 'Student::enrollmentRequests');
    $routes->post('accept-enrollment', 'Student::acceptEnrollment');
    $routes->post('reject-enrollment', 'Student::rejectEnrollment');
});

// Admin Routes (Protected by roleauth filter)
$routes->group('admin', ['filter' => 'roleauth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('courses', 'Admin::courses');
    $routes->get('courses/create', 'Admin::createCourse');
    $routes->post('courses/store', 'Admin::storeCourse');
    $routes->get('courses/edit/(:num)', 'Admin::editCourse/$1');
    $routes->post('courses/update/(:num)', 'Admin::updateCourse/$1');
    $routes->get('courses/delete/(:num)', 'Admin::deleteCourse/$1');
    $routes->get('courses/assign-teacher/(:num)', 'Admin::assignTeacher/$1');
    $routes->post('courses/assign-teacher/(:num)', 'Admin::storeTeacherAssignment/$1');
    
    // Materials Management
    $routes->get('materials', 'Materials::index');
    
    // Enrollment Management Routes
    $routes->get('enrollments', 'Admin::enrollments');
    $routes->get('enrollments/view/(:num)', 'Admin::viewEnrollmentDetails/$1');
    $routes->get('enrollments/create/(:num)', 'Admin::enrollStudent/$1');
    $routes->post('enrollments/store', 'Admin::storeEnrollment');
    $routes->post('enrollments/remove/(:num)', 'Admin::removeEnrollment/$1');
    $routes->get('enrollments/course/(:num)', 'Admin::enrollStudent/$1');
    
    // Student Enrollment Management (similar to Teacher Assignments)
    $routes->get('student-enrollments', 'Admin::studentEnrollments');
    $routes->get('student-enrollments/(:alpha)', 'Admin::studentEnrollments/$1');
    $routes->post('student-enrollments/approve', 'Admin::approveStudentEnrollment');
    $routes->post('student-enrollments/reject', 'Admin::rejectStudentEnrollment');
    
    // Schedule Management Routes
    $routes->get('schedules', 'Admin::manageSchedules');
    $routes->post('schedules/update', 'Admin::updateSchedule');
    $routes->post('schedules/delete/(:num)', 'Admin::deleteSchedule/$1');
    $routes->get('schedules/cancel', 'Admin::cancelAssignment');
    $routes->get('assignments', 'Admin::viewAssignments');
    $routes->get('assignments/details/(:num)', 'Admin::getAssignmentDetails/$1');
    $routes->post('assignments/cancel/(:num)', 'Admin::cancelAssignmentById/$1');
    
    // Add more admin-only routes here
});

// User Management Routes (Admin only)
$routes->group('users', ['filter' => 'roleauth:admin'], function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->get('delete/(:num)', 'UserController::delete/$1');
    $routes->get('trash', 'UserController::trash');
    $routes->get('restore/(:num)', 'UserController::restore/$1');
});

$routes->group('student', ['filter' => 'roleauth:student'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('my-schedule', 'Student::mySchedule');
    $routes->get('my-courses', 'Student::myCourses');
    $routes->get('materials', 'Student::materials');
    $routes->get('enrollment-requests', 'Student::enrollmentRequests');
    $routes->post('enrollment/accept', 'Student::acceptEnrollment');
    $routes->post('enrollment/reject', 'Student::rejectEnrollment');
    // Add more student-only routes here
});

// Materials management and access
$routes->get('/materials', 'Materials::index');
$routes->get('/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// Optional: Course materials listing for enrolled students
$routes->get('/course/(:num)/materials', 'Materials::listing/$1');

// Notifications API
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Approval System Routes
$routes->group('approval', ['filter' => 'auth'], function($routes) {
    // Teacher Assignment Approval
    $routes->get('assignment/respond/(:num)', 'ApprovalController::respondToAssignment/$1');
    $routes->post('assignment/submit/(:num)', 'ApprovalController::submitAssignmentResponse/$1');
    $routes->get('teacher/assignments', 'ApprovalController::teacherAssignments');
    
    // Student Enrollment Approval
    $routes->get('enrollment/respond/(:num)', 'ApprovalController::respondToEnrollment/$1');
    $routes->post('enrollment/submit/(:num)', 'ApprovalController::submitEnrollmentResponse/$1');
    $routes->get('student/enrollments', 'ApprovalController::studentEnrollments');
});
