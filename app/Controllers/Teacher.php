<?php

namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Controller;

class Teacher extends Controller
{
    public function dashboard()
    {
        $session = session();

        // Authorization check
        if ($session->get('role') !== 'teacher') {
            return redirect()->to('/auth/login')->with('error', 'Unauthorized access.');
        }

        $teacherId = $session->get('id');

        // Fetch courses 
        $courseModel = new CourseModel();
        $courses = $courseModel->where('instructor_id', $teacherId)->findAll();

        // No DB yet
        $notifications = [
        ];

        $data = [
            'user'          => $session->get(),
            'courses'       => $courses,
            'notifications' => $notifications,
        ];

        return view('teacher/dashboard', $data);
    }

    public function createCourse()
    {
        $session = session();

        // Only teachers can access
        if ($session->get('role') !== 'teacher') {
            return redirect()->to('/auth/login')->with('error', 'Unauthorized access.');
        }

        // Add form + DB insert for new course later
        return view('teacher/create_course');
    }
}
