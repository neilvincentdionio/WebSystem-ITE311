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

        // If logged in and role is admin, show dashboard
        return view('admin_dashboard');
    }
}
