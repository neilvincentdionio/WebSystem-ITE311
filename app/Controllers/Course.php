<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $db;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->db = \Config\Database::connect();
        helper(['url', 'form', 'session']);
    }

    /**
     * Handle AJAX enrollment for students (without CourseModel)
     */
    public function enroll()
    {
        $session = session();
        $user_id = $session->get('id');

        if (!$user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll.'
            ]);
        }

        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No course selected.'
            ]);
        }

        // Check if the course exists in the DB
        $course = $this->db->table('courses')->where('id', $course_id)->get()->getRowArray();
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // Prevent duplicate enrollment
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Insert enrollment
        $data = [
            'user_id'     => $user_id,
            'course_id'   => $course_id,
            'enrolled_at' => date('Y-m-d H:i:s')
        ];

        $inserted = $this->enrollmentModel->insert($data);

        if ($inserted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment successful!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll. Please try again.'
            ]);
        }
    }
}
