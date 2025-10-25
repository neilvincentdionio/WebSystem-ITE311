<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        // Check if user role is admin
        $role = strtolower($session->get('role')); // make comparison case-insensitive
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }

        // Load courses for quick access to uploads
        $db = \Config\Database::connect();
        $courses = $db->table('courses')->orderBy('id', 'ASC')->get()->getResultArray();

        // If logged in and role is admin, show dashboard with courses
        return view('admin/admin_dashboard', [
            'courses' => $courses,
        ]);
    }

    public function courses()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $db = \Config\Database::connect();
        $courses = $db->table('courses')->orderBy('id', 'ASC')->get()->getResultArray();
        return view('admin/admin_courses', [
            'courses' => $courses,
        ]);
    }
}
