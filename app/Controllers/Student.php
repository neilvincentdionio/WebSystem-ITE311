<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Student extends BaseController
{
    protected $db;
    protected $enrollments;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->enrollments = new EnrollmentModel();
    }

    public function dashboard()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        
        // Get pending enrollment requests for this student
        $pendingEnrollments = [];
        try {
            $pendingEnrollments = $this->db->table('student_enrollments e')
                ->select('e.*, c.title as course_title, c.course_code, c.description, u.name as teacher_name')
                ->join('courses c', 'c.id = e.course_id')
                ->join('users u', 'u.id = c.teacher_id')
                ->where('e.student_id', $studentId)
                ->where('e.status', 'pending')
                ->orderBy('e.enrolled_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Student pending enrollments error: {err}', ['err' => $e->getMessage()]);
        }

        // Get approved courses count
        $approvedCourses = 0;
        try {
            $approvedCourses = $this->db->table('student_enrollments')
                ->where('student_id', $studentId)
                ->where('status', 'approved')
                ->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', 'Student approved courses error: {err}', ['err' => $e->getMessage()]);
        }

        // Get active courses count (courses with materials)
        $activeCourses = 0;
        try {
            $activeCourses = $this->db->table('student_enrollments e')
                ->select('COUNT(DISTINCT e.course_id) as count')
                ->join('materials m', 'm.course_id = e.course_id')
                ->where('e.student_id', $studentId)
                ->where('e.status', 'approved')
                ->get()
                ->getRowArray()['count'] ?? 0;
        } catch (\Throwable $e) {
            log_message('error', 'Student active courses error: {err}', ['err' => $e->getMessage()]);
        }

        return view('student/dashboard', [
            'student_name' => $session->get('name'),
            'pending_enrollments' => $pendingEnrollments,
            'approved_courses' => $approvedCourses,
            'active_courses' => $activeCourses
        ]);
    }

    public function enrollmentRequests()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        
        // Get all enrollment requests for this student
        $enrollments = [];
        try {
            $enrollments = $this->db->table('student_enrollments e')
                ->select('e.*, c.title as course_title, c.course_code, c.description, u.name as teacher_name')
                ->join('courses c', 'c.id = e.course_id')
                ->join('users u', 'u.id = c.teacher_id')
                ->where('e.student_id', $studentId)
                ->orderBy('e.enrolled_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Student enrollment requests error: {err}', ['err' => $e->getMessage()]);
        }

        return view('student/enrollment_requests', [
            'enrollments' => $enrollments,
            'student_name' => $session->get('name')
        ]);
    }

    public function acceptEnrollment()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            // Verify enrollment belongs to this student
            $enrollment = $this->db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->where('student_id', $studentId)
                ->where('status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return redirect()->to('student/enrollment-requests')->with('error', 'Enrollment request not found or already processed');
            }

            // Update enrollment status
            $this->db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'approved',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Enrollment accepted by student',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            return redirect()->to('student/enrollment-requests')->with('success', 'Enrollment accepted successfully');

        } catch (\Throwable $e) {
            log_message('error', 'Accept enrollment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('student/enrollment-requests')->with('error', 'Failed to accept enrollment');
        }
    }

    public function rejectEnrollment()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            // Verify enrollment belongs to this student
            $enrollment = $this->db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->where('student_id', $studentId)
                ->where('status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return redirect()->to('student/enrollment-requests')->with('error', 'Enrollment request not found or already processed');
            }

            // Update enrollment status
            $this->db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'rejected',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Enrollment rejected by student',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            return redirect()->to('student/enrollment-requests')->with('success', 'Enrollment rejected');

        } catch (\Throwable $e) {
            log_message('error', 'Reject enrollment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('student/enrollment-requests')->with('error', 'Failed to reject enrollment');
        }
    }

    public function mySchedule()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        
        // Get approved courses with schedule information
        $courses = [];
        try {
            $courses = $this->db->table('student_enrollments se')
                ->select('se.*, c.course_code, c.title, c.day_range, c.start_time, c.end_time, c.building, c.room_number, u.name as teacher_name, u.email as teacher_email')
                ->join('courses c', 'c.id = se.course_id')
                ->join('users u', 'u.id = c.teacher_id', 'left')
                ->where('se.student_id', $studentId)
                ->where('se.status', 'approved')
                ->orderBy('c.day_range', 'ASC')
                ->orderBy('c.start_time', 'ASC')
                ->get()
                ->getResultArray();
                
            // Format schedule for each course
            foreach ($courses as &$course) {
                if ($course['day_range'] && $course['start_time'] && $course['end_time']) {
                    $course['schedule'] = $course['day_range'] . ' ' . 
                        date('h:i A', strtotime($course['start_time'])) . ' - ' . 
                        date('h:i A', strtotime($course['end_time']));
                } else {
                    $course['schedule'] = 'Schedule not set';
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'My schedule error: {err}', ['err' => $e->getMessage()]);
        }

        return view('student/my_schedule', [
            'student_name' => $session->get('name'),
            'courses' => $courses
        ]);
    }

    public function myCourses()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        
        // Get approved courses
        $courses = [];
        try {
            $courses = $this->db->table('student_enrollments se')
                ->select('se.*, c.course_code, c.title, c.description, c.department, c.program, c.semester, c.term, u.name as teacher_name, u.email as teacher_email')
                ->join('courses c', 'c.id = se.course_id')
                ->join('users u', 'u.id = c.teacher_id', 'left')
                ->where('se.student_id', $studentId)
                ->where('se.status', 'approved')
                ->orderBy('c.title', 'ASC')
                ->get()
                ->getResultArray();
                
            // Get materials count for each course
            foreach ($courses as &$course) {
                $materialsCount = $this->db->table('materials')
                    ->where('course_id', $course['course_id'])
                    ->countAllResults();
                $course['materials_count'] = $materialsCount;
            }
        } catch (\Throwable $e) {
            log_message('error', 'My courses error: {err}', ['err' => $e->getMessage()]);
        }

        return view('student/my_courses', [
            'student_name' => $session->get('name'),
            'courses' => $courses
        ]);
    }

    public function materials()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        
        // Get materials from approved courses
        $materials = [];
        try {
            // Debug: Log the student ID and query
            log_message('debug', 'Student materials: Student ID: ' . $studentId);
            
            // First, check if student has any approved enrollments
            $approvedEnrollments = $this->db->table('student_enrollments')
                ->where('student_id', $studentId)
                ->where('status', 'approved')
                ->get()
                ->getResultArray();
            
            log_message('debug', 'Student materials: Approved enrollments found: ' . count($approvedEnrollments));
            
            $materials = $this->db->table('materials m')
                ->select('m.*, c.course_code, c.title as course_title')
                ->join('courses c', 'c.id = m.course_id')
                ->join('student_enrollments se', 'se.course_id = c.id AND se.student_id = ' . $studentId . ' AND se.status = "approved"', 'inner')
                ->orderBy('c.course_code', 'ASC')
                ->orderBy('m.exam_type', 'ASC')
                ->orderBy('m.created_at', 'DESC')
                ->get()
                ->getResultArray();
                
            log_message('debug', 'Student materials: Materials found: ' . count($materials));
                
            // Organize materials by category
            $organizedMaterials = [
                'prelim' => [],
                'midterm' => [],
                'final' => [],
                'others' => []
            ];
            
            foreach ($materials as $material) {
                $category = strtolower($material['exam_type'] ?? 'others');
                // Map exam_type to our categories
                if ($category === 'finals') {
                    $category = 'final';
                }
                if (in_array($category, ['prelim', 'midterm', 'final'])) {
                    $organizedMaterials[$category][] = $material;
                } else {
                    $organizedMaterials['others'][] = $material;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Student materials error: {err}', ['err' => $e->getMessage()]);
            $organizedMaterials = [
                'prelim' => [],
                'midterm' => [],
                'final' => [],
                'others' => []
            ];
        }

        return view('student/materials', [
            'student_name' => $session->get('name'),
            'materials' => $organizedMaterials
        ]);
    }
}
