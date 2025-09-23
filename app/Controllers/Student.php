<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Student extends Controller
{
    public function dashboard()
    {
        $session = session();

        // Auth check: Only students can access
        if ($session->get('role') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Unauthorized access.');
        }

        $studentId = $session->get('id');

        // No DB yet
        $courses = [
        ];

        // No DB yet
        $deadlines = [
        ];

        // No DB yet
        $grades = [
        ];

        $data = [
            'user'      => $session->get(),
            'courses'   => $courses,
            'deadlines' => $deadlines,
            'grades'    => $grades,
        ];

        return view('student/dashboard', $data);
    }
}
