<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;

class Admin extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        // Authorization check - ensure user has admin role
        if ($session->get('role') !== UserModel::ROLE_ADMIN) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        // Load models
        $userModel   = new UserModel();
        $courseModel = new CourseModel();

        // Statistics for cards
        $statistics = [
            'total_users'   => $userModel->countAllResults(),
            'total_courses' => $courseModel->countAllResults(),
        ];

        // No DB yet 
        $recent_activity = [];

        // Pass data to view
        return view('admin/dashboard', [
            'title'           => 'Admin Dashboard',
            'user'            => $session->get(),
            'statistics'      => $statistics,
            'recent_activity' => $recent_activity,
        ]);
    }

    // Manage Users page
    public function manageUsers()
    {
        $session = session();

        // Authorization check
        if (!$session->get('isLoggedIn') || $session->get('role') !== UserModel::ROLE_ADMIN) {
            return redirect()->to('/auth/login')->with('error', 'Access denied.');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('admin/manage_users', [
            'title' => 'Manage Users',
            'users' => $users,
            'user'  => $session->get(),
        ]);
    }

    // Manage Courses page
    public function manageCourses()
    {
        $session = session();

        // Authorization check
        if (!$session->get('isLoggedIn') || $session->get('role') !== UserModel::ROLE_ADMIN) {
            return redirect()->to('/auth/login')->with('error', 'Access denied.');
        }

        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();

        return view('admin/manage_courses', [
            'title'   => 'Manage Courses',
            'courses' => $courses,
            'user'    => $session->get(),
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully.');
    }
}
