<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        // Load user info from session
        $data = [
            'title' => 'Dashboard',
            'user'  => [
                'id'    => session()->get('id'),
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role'),
            ],
        ];

        // Correct view file
        return view('auth/dashboard', $data);
    }
}
