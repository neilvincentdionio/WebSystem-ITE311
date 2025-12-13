<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\StudentEnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $db;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new StudentEnrollmentModel();
        $this->db = \Config\Database::connect();
        helper(['url', 'form', 'session']);
    }

    /**
     * Display courses listing page with search interface
     */
    public function index()
    {
        // Get all courses for initial display
        $courses = $this->courseModel->findAll();
        
        return view('courses/index', [
            'courses' => $courses,
            'title' => 'Courses - LMS'
        ]);
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
            'student_id'  => $user_id,
            'course_id'   => $course_id,
            'status'      => 'pending',
            'enrolled_at' => date('Y-m-d H:i:s')
        ];

        $inserted = $this->enrollmentModel->insert($data);

        if ($inserted) {
            // Create a notification for the student
            try {
                $notifModel = new NotificationModel();
                $notifModel->insert([
                    'user_id'    => (int) $user_id,
                    'message'    => 'You have been enrolled in ' . ($course['title'] ?? 'a course'),
                    'is_read'    => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // Silently ignore notification failures to not block enrollment
            }

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

    /**
     * Display course details page
     */
    public function show($id = null)
    {
        if (!$id) {
            return redirect()->to('courses')->with('error', 'Course not found.');
        }

        // Get course details
        $course = $this->courseModel->find($id);
        if (!$course) {
            return redirect()->to('courses')->with('error', 'Course not found.');
        }

        // Get teacher information
        $teacher = null;
        if (!empty($course['teacher_id'])) {
            $teacher = $this->db->table('users')
                ->where('id', $course['teacher_id'])
                ->get()
                ->getRowArray();
        }

        // Get materials count
        $materialsCount = $this->db->table('materials')
            ->where('course_id', $id)
            ->countAllResults();

        // Get enrolled students count
        $enrolledCount = $this->db->table('student_enrollments')
            ->where('course_id', $id)
            ->where('status', 'approved')
            ->countAllResults();

        // Check if current student is enrolled
        $session = session();
        $isEnrolled = false;
        $enrollmentStatus = null;
        
        if ($session->get('isLoggedIn') && strtolower($session->get('role')) === 'student') {
            $studentId = $session->get('id');
            $enrollment = $this->db->table('student_enrollments')
                ->where('course_id', $id)
                ->where('student_id', $studentId)
                ->get()
                ->getRowArray();
            
            if ($enrollment) {
                $isEnrolled = true;
                $enrollmentStatus = $enrollment['status'];
            }
        }

        return view('courses/show', [
            'course' => $course,
            'teacher' => $teacher,
            'materialsCount' => $materialsCount,
            'enrolledCount' => $enrolledCount,
            'isEnrolled' => $isEnrolled,
            'enrollmentStatus' => $enrollmentStatus,
            'title' => 'Course Details - ' . $course['title']
        ]);
    }

    /**
     * Search courses by title or description
     * Supports both AJAX (JSON) and regular (view) requests
     */
    public function search()
    {
        // Retrieve the search term from GET request
        $searchTerm = $this->request->getGet('search_term');

        // 2. If a search term is provided, apply LIKE queries
        if (!empty($searchTerm)) {
            $this->courseModel->like('title', $searchTerm);
            $this->courseModel->orLike('description', $searchTerm);
        }

        // Fetch courses based on applied conditions (or all if no search term)
        $courses = $this->courseModel->findAll();

        // Check if the request is AJAX
        if ($this->request->isAJAX()) {
            // If AJAX, return structured JSON response
            return $this->response->setJSON([
                'success' => true,
                'term' => $searchTerm ?? '',
                'count' => count($courses),
                'results' => $courses
            ]);
        }

        // If not AJAX, render a view with the results
        return view('courses/search_results', [
            'courses' => $courses, 
            'searchTerm' => $searchTerm,
            'title' => 'Course Search - LMS'
        ]);
    }
}
