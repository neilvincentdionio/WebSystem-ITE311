<?php

namespace App\Controllers;

use App\Models\TeacherAssignmentModel;
use App\Models\StudentEnrollmentModel;

class ApprovalController extends BaseController
{
    protected $teacherAssignmentModel;
    protected $studentEnrollmentModel;

    public function __construct()
    {
        $this->teacherAssignmentModel = new TeacherAssignmentModel();
        $this->studentEnrollmentModel = new StudentEnrollmentModel();
    }

    // Teacher Assignment Approval Methods
    public function respondToAssignment($assignmentId)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // Get assignment details
        $assignment = $this->teacherAssignmentModel->getAssignmentDetails($assignmentId);
        if (!$assignment) {
            $session->setFlashdata('error', 'Assignment not found.');
            return redirect()->back();
        }

        // Check if the current user is the assigned teacher
        if ($session->get('user_id') != $assignment['teacher_id']) {
            $session->setFlashdata('error', 'Access Denied: You can only respond to your own assignments.');
            return redirect()->back();
        }

        // Check if assignment is still pending
        if ($assignment['status'] !== 'pending') {
            $session->setFlashdata('error', 'This assignment has already been responded to.');
            return redirect()->back();
        }

        return view('approvals/assignment_response', ['assignment' => $assignment]);
    }

    public function submitAssignmentResponse($assignmentId)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'response' => 'required|in_list[approved,rejected]',
            'message' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        $response = $this->request->getPost('response');
        $message = $this->request->getPost('message');

        // Get assignment details to verify ownership
        $assignment = $this->teacherAssignmentModel->find($assignmentId);
        if (!$assignment || $session->get('id') != $assignment['teacher_id']) {
            $session->setFlashdata('error', 'Invalid assignment or access denied.');
            return redirect()->back();
        }

        // Update assignment response
        if ($this->teacherAssignmentModel->respondToAssignment($assignmentId, $response, $message)) {
            $statusText = $response === 'approved' ? 'approved' : 'rejected';
            $session->setFlashdata('success', "Assignment {$statusText} successfully!");
            
            // If approved, update the course teacher_id
            if ($response === 'approved') {
                $db = \Config\Database::connect();
                $db->table('courses')->where('id', $assignment['course_id'])->update(['teacher_id' => $assignment['teacher_id']]);
            }
        } else {
            $session->setFlashdata('error', 'Failed to submit response.');
        }

        return redirect()->to('/teacher/dashboard');
    }

    // Student Enrollment Approval Methods
    public function respondToEnrollment($enrollmentId)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // Get enrollment details
        $enrollment = $this->studentEnrollmentModel->getEnrollmentDetails($enrollmentId);
        if (!$enrollment) {
            $session->setFlashdata('error', 'Enrollment not found.');
            return redirect()->back();
        }

        // Check if the current user is the enrolled student
        if ($session->get('user_id') != $enrollment['student_id']) {
            $session->setFlashdata('error', 'Access Denied: You can only respond to your own enrollments.');
            return redirect()->back();
        }

        // Check if enrollment is still pending
        if ($enrollment['status'] !== 'pending') {
            $session->setFlashdata('error', 'This enrollment has already been responded to.');
            return redirect()->back();
        }

        return view('approvals/enrollment_response', ['enrollment' => $enrollment]);
    }

    public function submitEnrollmentResponse($enrollmentId)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'response' => 'required|in_list[approved,rejected]',
            'message' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        $response = $this->request->getPost('response');
        $message = $this->request->getPost('message');

        // Get enrollment details to verify ownership
        $enrollment = $this->studentEnrollmentModel->find($enrollmentId);
        if (!$enrollment || $session->get('id') != $enrollment['student_id']) {
            $session->setFlashdata('error', 'Invalid enrollment or access denied.');
            return redirect()->back();
        }

        // Update enrollment response
        if ($this->studentEnrollmentModel->respondToEnrollment($enrollmentId, $response, $message)) {
            $statusText = $response === 'approved' ? 'approved' : 'rejected';
            $session->setFlashdata('success', "Enrollment {$statusText} successfully!");
            
            // If approved, add to the main enrollments table
            if ($response === 'approved') {
                $db = \Config\Database::connect();
                $db->table('enrollments')->insert([
                    'course_id' => $enrollment['course_id'],
                    'user_id' => $enrollment['student_id'],
                    'semester' => $enrollment['semester'],
                    'term' => $enrollment['term'],
                    'enrolled_at' => $enrollment['enrolled_at']
                ]);
            }
        } else {
            $session->setFlashdata('error', 'Failed to submit response.');
        }

        return redirect()->to('/student/dashboard');
    }

    // View pending assignments for teachers
    public function teacherAssignments()
    {
        $session = session();
        
        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/auth/login');
        }

        $teacherId = $session->get('user_id');
        $assignments = $this->teacherAssignmentModel->getTeacherAssignments($teacherId);

        return view('approvals/teacher_assignments', ['assignments' => $assignments]);
    }

    // View pending enrollments for students
    public function studentEnrollments()
    {
        $session = session();
        
        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'student') {
            return redirect()->to('/auth/login');
        }

        $studentId = $session->get('user_id');
        $enrollments = $this->studentEnrollmentModel->getStudentEnrollments($studentId);

        return view('approvals/student_enrollments', ['enrollments' => $enrollments]);
    }
}
