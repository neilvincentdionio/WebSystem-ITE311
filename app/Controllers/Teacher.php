<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use App\Models\GradingModel;

class Teacher extends BaseController
{
    protected $db;
    protected $materials;
    protected $enrollments;
    protected $grading;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->materials = new MaterialModel();
        $this->enrollments = new EnrollmentModel();
        $this->grading = new GradingModel();
    }

    public function dashboard()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Get teacher's courses
        $courses = [];
        try {
            $courses = $this->db->table('courses')
                ->where('teacher_id', $teacherId)
                ->get()
                ->getResultArray();
            
            // Construct schedule string for each course
            foreach ($courses as &$course) {
                if ($course['day_range'] && $course['start_time'] && $course['end_time']) {
                    $course['schedule'] = $course['day_range'] . ' ' . 
                        date('h:i A', strtotime($course['start_time'])) . ' - ' . 
                        date('h:i A', strtotime($course['end_time']));
                } else {
                    $course['schedule'] = 'N/A';
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Teacher dashboard error: {err}', ['err' => $e->getMessage()]);
        }

        // Get pending assignments
        $pendingAssignments = [];
        try {
            $pendingAssignments = $this->db->table('teacher_assignments ta')
                ->select('ta.*, c.course_code, c.title, c.day_range, c.start_time, c.end_time, c.academic_year, c.semester, u.name as assigned_by_name')
                ->join('courses c', 'c.id = ta.course_id')
                ->join('users u', 'u.id = ta.assigned_by')
                ->where('ta.teacher_id', $teacherId)
                ->where('ta.status', 'pending')
                ->orderBy('ta.assigned_at', 'DESC')
                ->get()
                ->getResultArray();

            // Construct schedule string for each assignment
            foreach ($pendingAssignments as &$assignment) {
                if ($assignment['day_range'] && $assignment['start_time'] && $assignment['end_time']) {
                    $assignment['schedule'] = $assignment['day_range'] . ' ' . 
                        date('h:i A', strtotime($assignment['start_time'])) . ' - ' . 
                        date('h:i A', strtotime($assignment['end_time']));
                } else {
                    $assignment['schedule'] = 'N/A';
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Pending assignments error: {err}', ['err' => $e->getMessage()]);
        }

        // Get all enrollment requests (pending, approved, rejected)
        $allEnrollments = [];
        try {
            $allEnrollments = $this->enrollments->getEnrollmentRequests($teacherId, 'all');
        } catch (\Throwable $e) {
            log_message('error', 'Enrollment requests error: {err}', ['err' => $e->getMessage()]);
        }

        // Get statistics
        $stats = [
            'total_courses' => count($courses),
            'total_students' => 0,
            'total_materials' => 0,
            'recent_activities' => [],
            'pending_assignments' => count($pendingAssignments)
        ];

        // Count students and materials
        foreach ($courses as $course) {
            // Count enrolled students
            try {
                $studentCount = $this->db->table('enrollments')
                    ->where('course_id', $course['id'])
                    ->countAllResults();
                $stats['total_students'] += $studentCount;
            } catch (\Throwable $e) {
                log_message('error', 'Student count error: {err}', ['err' => $e->getMessage()]);
            }
            
            // Count materials
            try {
                $materialCount = $this->db->table('materials')
                    ->where('course_id', $course['id'])
                    ->countAllResults();
                $stats['total_materials'] += $materialCount;
            } catch (\Throwable $e) {
                log_message('error', 'Material count error: {err}', ['err' => $e->getMessage()]);
            }
        }

        // Get recent activities (simplified)
        $stats['recent_activities'] = [
            ['type' => 'upload', 'description' => 'Materials uploaded', 'time' => '2 hours ago'],
            ['type' => 'enrollment', 'description' => 'New student enrolled', 'time' => '5 hours ago'],
            ['type' => 'course', 'description' => 'Course created', 'time' => '1 day ago']
        ];

        return view('teacher/dashboard', [
            'courses' => $courses,
            'stats' => $stats,
            'teacher_name' => $session->get('name'),
            'pending_assignments' => $pendingAssignments,
            'pending_enrollments' => $allEnrollments
        ]);
    }

    public function courses()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Get teacher's courses
        $courses = [];
        try {
            $courses = $this->db->table('courses')
                ->where('teacher_id', $teacherId)
                ->get()
                ->getResultArray();
            
            // Construct schedule string for each course
            foreach ($courses as &$course) {
                if ($course['day_range'] && $course['start_time'] && $course['end_time']) {
                    $course['schedule'] = $course['day_range'] . ' ' . 
                        date('h:i A', strtotime($course['start_time'])) . ' - ' . 
                        date('h:i A', strtotime($course['end_time']));
                } else {
                    $course['schedule'] = 'N/A';
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Teacher courses error: {err}', ['err' => $e->getMessage()]);
        }

        // Get all students for enrollment dropdown
        $students = [];
        try {
            $students = $this->db->table('users')
                ->where('role', 'student')
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Error getting students: {err}', ['err' => $e->getMessage()]);
        }

        return view('teacher/courses', [
            'courses' => $courses,
            'students' => $students,
            'enrollments' => $this->enrollments,
            'materials' => $this->materials
        ]);
    }

    public function enrollStudent()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $courseId = $this->request->getPost('course_id');
        $studentId = $this->request->getPost('student_id');
        $notes = $this->request->getPost('notes');

        if (!$courseId || !$studentId) {
            return redirect()->to('teacher/courses')->with('error', 'Course and student are required');
        }

        try {
            // Verify course belongs to teacher
            $course = $this->db->table('courses')
                ->where('id', $courseId)
                ->where('teacher_id', $teacherId)
                ->get()
                ->getRowArray();

            if (!$course) {
                return redirect()->to('teacher/courses')->with('error', 'Course not found or not assigned to you');
            }

            // Verify student exists and is active
            $student = $this->db->table('users')
                ->where('id', $studentId)
                ->where('role', 'student')
                ->get()
                ->getRowArray();

            if (!$student) {
                return redirect()->to('teacher/courses')->with('error', 'Student not found');
            }

            // Check if student is already enrolled
            if ($this->enrollments->hasExistingRequest($studentId, $courseId)) {
                return redirect()->to('teacher/courses')->with('error', 'Student already has a pending or approved enrollment for this course');
            }

            // Create enrollment request (pending by default since teacher is initiating)
            $data = [
                'user_id' => $studentId,
                'course_id' => $courseId,
                'enrolled_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'response_message' => $notes ? "Teacher note: " . $notes : "Teacher note: Requested by teacher",
                'enrolled_by' => $teacherId
            ];

            if ($this->enrollments->createRequest($data)) {
                return redirect()->to('teacher/courses')->with('success', 'Enrollment request sent to student for ' . $course['title']);
            } else {
                return redirect()->to('teacher/courses')->with('error', 'Failed to create enrollment request');
            }

        } catch (\Throwable $e) {
            log_message('error', 'Enroll student error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('teacher/courses')->with('error', 'Failed to enroll student');
        }
    }

    public function students()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Get students enrolled in teacher's courses (approved only)
        $students = [];
        try {
            $students = $this->db->table('enrollments e')
                ->select('e.*, u.name as student_name, u.email as student_email, c.course_code, c.title as course_title')
                ->join('users u', 'u.id = e.user_id')
                ->join('courses c', 'c.id = e.course_id')
                ->where('c.teacher_id', $teacherId)
                ->where('e.status', 'approved')
                ->orderBy('c.course_code', 'ASC')
                ->orderBy('u.name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Teacher students error: {err}', ['err' => $e->getMessage()]);
        }

        return view('teacher/students', ['students' => $students]);
    }

    public function schedule()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Get teacher's assigned courses with schedule info
        $courses = [];
        try {
            // Get courses directly assigned to teacher
            $directCourses = $this->db->table('courses c')
                ->select('c.*, ta.responded_at')
                ->join('teacher_assignments ta', 'ta.course_id = c.id AND ta.teacher_id = ' . $teacherId, 'left')
                ->where('c.teacher_id', $teacherId)
                ->where('c.schedule_status', 'active')
                ->orderBy('c.start_time', 'ASC')
                ->get()
                ->getResultArray();
            
            // Get approved assignments
            $assignmentCourses = $this->db->table('teacher_assignments ta')
                ->select('c.*, ta.status as assignment_status, ta.responded_at')
                ->join('courses c', 'c.id = ta.course_id')
                ->where('ta.teacher_id', $teacherId)
                ->where('ta.status', 'approved')
                ->orderBy('c.start_time', 'ASC')
                ->get()
                ->getResultArray();
            
            // Merge and remove duplicates
            $allCourses = array_merge($directCourses, $assignmentCourses);
            $uniqueCourses = [];
            $seenIds = [];
            
            foreach ($allCourses as $course) {
                if (!in_array($course['id'], $seenIds)) {
                    $uniqueCourses[] = $course;
                    $seenIds[] = $course['id'];
                }
            }
            
            $courses = $uniqueCourses;
            
            // Construct schedule string for each course
            foreach ($courses as &$course) {
                if ($course['day_range'] && $course['start_time'] && $course['end_time']) {
                    $course['schedule'] = $course['day_range'] . ' ' . 
                        date('h:i A', strtotime($course['start_time'])) . ' - ' . 
                        date('h:i A', strtotime($course['end_time']));
                } else {
                    $course['schedule'] = 'N/A';
                }
                
                // Ensure responded_at is always set
                if (!isset($course['responded_at']) || empty($course['responded_at'])) {
                    $course['responded_at'] = null;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Teacher schedule error: {err}', ['err' => $e->getMessage()]);
        }

        return view('teacher/schedule', ['courses' => $courses]);
    }

    public function acceptAssignment()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $assignmentId = $this->request->getPost('assignment_id');

        if (!$assignmentId) {
            return redirect()->to('teacher/dashboard')->with('error', 'Invalid assignment ID');
        }

        try {
            // Get assignment details
            $assignment = $this->db->table('teacher_assignments')
                ->where('id', $assignmentId)
                ->where('teacher_id', $teacherId)
                ->where('status', 'pending')
                ->get()
                ->getRowArray();

            if (!$assignment) {
                return redirect()->to('teacher/dashboard')->with('error', 'Assignment not found or already processed');
            }

            // Update assignment status
            $this->db->table('teacher_assignments')
                ->where('id', $assignmentId)
                ->update([
                    'status' => 'approved',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Assignment accepted by teacher'
                ]);

            // Update course teacher_id and schedule_status
            $this->db->table('courses')
                ->where('id', $assignment['course_id'])
                ->update([
                    'teacher_id' => $teacherId,
                    'schedule_status' => 'active'
                ]);

            return redirect()->to('teacher/dashboard')->with('success', 'Course assignment accepted successfully!');

        } catch (\Throwable $e) {
            log_message('error', 'Accept assignment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('teacher/dashboard')->with('error', 'Failed to accept assignment');
        }
    }

    public function rejectAssignment()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $assignmentId = $this->request->getPost('assignment_id');

        if (!$assignmentId) {
            return redirect()->to('teacher/dashboard')->with('error', 'Invalid assignment ID');
        }

        try {
            // Get assignment details
            $assignment = $this->db->table('teacher_assignments')
                ->where('id', $assignmentId)
                ->where('teacher_id', $teacherId)
                ->where('status', 'pending')
                ->get()
                ->getRowArray();

            if (!$assignment) {
                return redirect()->to('teacher/dashboard')->with('error', 'Assignment not found or already processed');
            }

            // Update assignment status
            $this->db->table('teacher_assignments')
                ->where('id', $assignmentId)
                ->update([
                    'status' => 'rejected',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Assignment rejected by teacher'
                ]);

            return redirect()->to('teacher/dashboard')->with('success', 'Course assignment rejected');

        } catch (\Throwable $e) {
            log_message('error', 'Reject assignment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('teacher/dashboard')->with('error', 'Failed to reject assignment');
        }
    }

    /**
     * View enrollment requests
     */
    public function enrollmentRequests($status = 'all')
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        try {
            $enrollments = $this->enrollments->getEnrollmentRequests($teacherId, $status);
            
            return view('teacher/enrollment_requests', [
                'enrollments' => $enrollments,
                'currentStatus' => $status
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Enrollment requests error: {err}', ['err' => $e->getMessage()]);
            return view('teacher/enrollment_requests', [
                'enrollments' => [],
                'currentStatus' => $status
            ]);
        }
    }

    /**
     * Accept enrollment request
     */
    public function acceptEnrollment()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            // Verify enrollment belongs to teacher's course
            $enrollment = $this->db->table('enrollments e')
                ->select('e.*, c.teacher_id')
                ->join('courses c', 'c.id = e.course_id')
                ->where('e.id', $enrollmentId)
                ->where('c.teacher_id', $teacherId)
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return redirect()->to('teacher/enrollment-requests')->with('error', 'Enrollment request not found');
            }

            // Update enrollment status
            $this->enrollments->updateStatus($enrollmentId, 'approved', 'Enrollment approved by teacher');

            return redirect()->to('teacher/enrollment-requests')->with('success', 'Student enrollment approved');

        } catch (\Throwable $e) {
            log_message('error', 'Accept enrollment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('teacher/enrollment-requests')->with('error', 'Failed to approve enrollment');
        }
    }

    /**
     * Reject enrollment request
     */
    public function rejectEnrollment()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            // Verify enrollment belongs to teacher's course
            $enrollment = $this->db->table('enrollments e')
                ->select('e.*, c.teacher_id')
                ->join('courses c', 'c.id = e.course_id')
                ->where('e.id', $enrollmentId)
                ->where('c.teacher_id', $teacherId)
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return redirect()->to('teacher/enrollment-requests')->with('error', 'Enrollment request not found');
            }

            // Update enrollment status
            $this->enrollments->updateStatus($enrollmentId, 'rejected', 'Enrollment rejected by teacher');

            return redirect()->to('teacher/enrollment-requests')->with('success', 'Student enrollment rejected');

        } catch (\Throwable $e) {
            log_message('error', 'Reject enrollment error: {err}', ['err' => $e->getMessage()]);
            return redirect()->to('teacher/enrollment-requests')->with('error', 'Failed to reject enrollment');
        }
    }

    /**
     * Create enrollment request (for students to request enrollment)
     */
    public function createEnrollmentRequest()
    {
        $session = session();
        $studentId = (int) $session->get('id');
        $courseId = $this->request->getPost('course_id');
        
        try {
            // Verify course exists and has a teacher
            $course = $this->db->table('courses')
                ->where('id', $courseId)
                ->where('teacher_id IS NOT NULL')
                ->get()
                ->getRowArray();

            if (!$course) {
                return redirect()->back()->with('error', 'Course not available for enrollment');
            }

            // Create enrollment request
            $data = [
                'user_id' => $studentId,
                'course_id' => $courseId,
                'enrolled_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'enrolled_by' => $studentId
            ];

            if ($this->enrollments->createRequest($data)) {
                return redirect()->back()->with('success', 'Enrollment request sent to teacher');
            } else {
                return redirect()->back()->with('error', 'You already have a pending or approved enrollment for this course');
            }

        } catch (\Throwable $e) {
            log_message('error', 'Create enrollment request error: {err}', ['err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create enrollment request');
        }
    }

    // Grading System Methods
    public function grading($courseId)
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Verify course belongs to teacher
        $course = $this->db->table('courses')
            ->where('id', $courseId)
            ->where('teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->to('teacher/courses')->with('error', 'Course not found');
        }

        $gradingPeriods = $this->grading->getGradingPeriods();
        $assignmentTypes = $this->grading->getAssignmentTypes();
        $activePeriod = $this->grading->getActiveGradingPeriod();

        return view('teacher/grading', [
            'course' => $course,
            'grading_periods' => $gradingPeriods,
            'assignment_types' => $assignmentTypes,
            'active_period' => $activePeriod
        ]);
    }

    public function setGradingWeights()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        $courseId = $this->request->getPost('course_id');
        $gradingPeriodId = $this->request->getPost('grading_period_id');
        $weights = $this->request->getPost('weights');

        // Verify course belongs to teacher
        $course = $this->db->table('courses')
            ->where('id', $courseId)
            ->where('teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Validate total weight equals 100
        $totalWeight = array_sum($weights);
        if ($totalWeight != 100) {
            return redirect()->back()->with('error', 'Total weight must equal 100%');
        }

        if ($this->grading->setGradingWeights($courseId, $gradingPeriodId, $weights)) {
            return redirect()->back()->with('success', 'Grading weights set successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to set grading weights');
        }
    }

    public function studentGrades($courseId, $gradingPeriodId = null)
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        // Verify course belongs to teacher
        $course = $this->db->table('courses')
            ->where('id', $courseId)
            ->where('teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->to('teacher/courses')->with('error', 'Course not found');
        }

        if (!$gradingPeriodId) {
            $activePeriod = $this->grading->getActiveGradingPeriod();
            $gradingPeriodId = $activePeriod['id'] ?? null;
        }

        $students = $this->grading->getStudentGrades($courseId, $gradingPeriodId);
        $gradingPeriods = $this->grading->getGradingPeriods();
        $assignmentTypes = $this->grading->getAssignmentTypes();
        $weights = $this->grading->getGradingWeights($courseId, $gradingPeriodId);
        
        // Get enrolled students for dropdown (separate from grades)
        $enrolledStudents = $this->db->table('enrollments e')
            ->select('e.user_id as student_id, u.name as student_name')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.course_id', $courseId)
            ->where('e.status', 'approved')
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('teacher/student_grades', [
            'course' => $course,
            'students' => $students,
            'enrolled_students' => $enrolledStudents,
            'grading_periods' => $gradingPeriods,
            'selected_period' => $gradingPeriodId,
            'assignment_types' => $assignmentTypes,
            'weights' => $weights
        ]);
    }

    public function saveGrade()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        $data = [
            'student_id' => $this->request->getPost('student_id'),
            'course_id' => $this->request->getPost('course_id'),
            'grading_period_id' => $this->request->getPost('grading_period_id'),
            'assignment_type_id' => $this->request->getPost('assignment_type_id'),
            'score' => $this->request->getPost('score'),
            'max_score' => $this->request->getPost('max_score'),
            'remarks' => $this->request->getPost('remarks'),
            'graded_by' => $teacherId
        ];

        // Verify course belongs to teacher
        $course = $this->db->table('courses')
            ->where('id', $data['course_id'])
            ->where('teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        if ($this->grading->saveStudentGrade($data)) {
            return redirect()->back()->with('success', 'Grade saved successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to save grade');
        }
    }

    public function saveBulkGrades()
    {
        $session = session();
        $teacherId = (int) $session->get('id');
        
        $courseId = $this->request->getPost('course_id');
        $gradingPeriodId = $this->request->getPost('grading_period_id');
        $assignmentTypeId = $this->request->getPost('assignment_type_id');
        $maxScore = $this->request->getPost('max_score');
        $remarks = $this->request->getPost('remarks');
        $grades = $this->request->getPost('grades');

        // Verify course belongs to teacher
        $course = $this->db->table('courses')
            ->where('id', $courseId)
            ->where('teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        if (empty($grades)) {
            return redirect()->back()->with('error', 'No grades provided');
        }

        $this->db->transStart();
        
        try {
            $savedCount = 0;
            foreach ($grades as $studentId => $score) {
                if ($score !== '' && $score !== null) {
                    $gradeData = [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'grading_period_id' => $gradingPeriodId,
                        'assignment_type_id' => $assignmentTypeId,
                        'score' => $score,
                        'max_score' => $maxScore,
                        'remarks' => $remarks,
                        'graded_by' => $teacherId
                    ];

                    if ($this->grading->saveStudentGrade($gradeData)) {
                        $savedCount++;
                    }
                }
            }

            $this->db->transComplete();
            
            if ($this->db->transStatus() && $savedCount > 0) {
                return redirect()->back()->with('success', "Successfully saved {$savedCount} grades");
            } else {
                return redirect()->back()->with('error', 'Failed to save grades');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Bulk grades error: {err}', ['err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save grades: ' . $e->getMessage());
        }
    }
}
