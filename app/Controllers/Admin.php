<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\TeacherAssignmentModel;
use App\Models\StudentEnrollmentModel;

class Admin extends BaseController
{
    protected $courseModel;
    protected $teacherAssignmentModel;
    protected $studentEnrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->teacherAssignmentModel = new TeacherAssignmentModel();
        $this->studentEnrollmentModel = new StudentEnrollmentModel();
    }

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

        // Get system overview statistics
        $db = \Config\Database::connect();
        
        // Course statistics
        $totalCourses = $this->courseModel->countAllResults();
        $coursesWithTeachers = $db->table('courses')->where('teacher_id IS NOT NULL')->countAllResults();
        
        // User statistics
        $totalUsers = $db->table('users')->countAllResults();
        $totalTeachers = $db->table('users')->where('role', 'teacher')->countAllResults();
        $totalStudents = $db->table('users')->where('role', 'student')->countAllResults();
        
        // Assignment statistics
        $assignmentStats = $this->teacherAssignmentModel->getAssignmentStats();
        
        // Enrollment statistics
        $enrollmentStats = $this->studentEnrollmentModel->getEnrollmentStats();
        
        // Recent activities
        $recentAssignments = $this->teacherAssignmentModel->getRecentAssignments();
        $recentEnrollments = $this->studentEnrollmentModel->getPendingEnrollments();
        
        // Load courses for quick access to uploads
        $courses = $this->courseModel->orderBy('id', 'ASC')->findAll();

        // If logged in and role is admin, show dashboard with system overview
        return view('admin/admin_dashboard', [
            'courses' => $courses,
            'stats' => [
                'courses' => [
                    'total' => $totalCourses,
                    'with_teachers' => $coursesWithTeachers,
                    'without_teachers' => $totalCourses - $coursesWithTeachers
                ],
                'users' => [
                    'total' => $totalUsers,
                    'teachers' => $totalTeachers,
                    'students' => $totalStudents,
                    'admins' => $totalUsers - $totalTeachers - $totalStudents
                ],
                'assignments' => $assignmentStats,
                'enrollments' => $enrollmentStats
            ],
            'recent_activities' => [
                'pending_assignments' => array_slice($recentAssignments, 0, 5),
                'pending_enrollments' => array_slice($recentEnrollments, 0, 5)
            ]
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

        $courses = $this->courseModel->orderBy('id', 'ASC')->findAll();
        return view('admin/admin_courses', [
            'courses' => $courses,
        ]);
    }

    public function createCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        // Get existing courses to extract unique departments and programs
        $db = \Config\Database::connect();
        
        try {
            // Get unique departments from existing courses
            $departments = $db->table('courses')
                            ->select('department')
                            ->where('department IS NOT NULL')
                            ->where('department !=', '')
                            ->groupBy('department')
                            ->orderBy('department', 'ASC')
                            ->get()
                            ->getResultArray();
            
            // Get unique programs from existing courses
            $programs = $db->table('courses')
                          ->select('program')
                          ->where('program IS NOT NULL')
                          ->where('program !=', '')
                          ->groupBy('program')
                          ->orderBy('program', 'ASC')
                          ->get()
                          ->getResultArray();
                           
        } catch (\Exception $e) {
            log_message('error', 'Error fetching departments/programs: ' . $e->getMessage());
            $departments = [];
            $programs = [];
        }

        return view('admin/course_create', [
            'departments' => $departments,
            'programs' => $programs
        ]);
    }

    public function storeCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]|max_length[150]',
            'description' => 'max_length[1000]',
            'course_code' => 'max_length[50]',
            'academic_year' => 'max_length[20]',
            'department' => 'max_length[100]',
            'program' => 'max_length[100]',
            'term' => 'max_length[20]',
            'semester' => 'max_length[50]',
            'schedule' => 'max_length[255]'
        ];

        // Custom validation for course title - no special characters allowed
        $title = $this->request->getPost('title');
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
            return redirect()->back()->withInput()->with('error', 'Course title can only contain letters, numbers, and spaces. Special characters are not allowed.');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => trim($this->request->getPost('title')),
            'description' => trim($this->request->getPost('description')),
            'course_code' => trim($this->request->getPost('course_code')),
            'academic_year' => trim($this->request->getPost('academic_year')),
            'department' => trim($this->request->getPost('department')),
            'program' => trim($this->request->getPost('program')),
            'term' => trim($this->request->getPost('term')),
            'semester' => trim($this->request->getPost('semester')),
            'schedule' => trim($this->request->getPost('schedule')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Debug: Log the data being inserted
        log_message('debug', 'Course creation data: ' . json_encode($data));

        try {
            $result = $this->courseModel->insert($data);
            if ($result) {
                $session->setFlashdata('success', 'Course created successfully!');
                log_message('debug', 'Course creation successful, ID: ' . $result);
            } else {
                $session->setFlashdata('error', 'Failed to create course. Please check all required fields.');
                log_message('error', 'Course creation failed. Data: ' . json_encode($data));
                log_message('error', 'CourseModel errors: ' . json_encode($this->courseModel->errors()));
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Database error: ' . $e->getMessage());
            log_message('error', 'Course creation exception: ' . $e->getMessage());
        }

        return redirect()->to('/admin/courses');
    }

    public function editCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/admin/courses');
        }

        // Get existing courses to extract unique departments and programs
        $db = \Config\Database::connect();
        
        try {
            // Get unique departments from existing courses
            $departments = $db->table('courses')
                            ->select('department')
                            ->where('department IS NOT NULL')
                            ->where('department !=', '')
                            ->groupBy('department')
                            ->orderBy('department', 'ASC')
                            ->get()
                            ->getResultArray();
            
            // Get unique programs from existing courses
            $programs = $db->table('courses')
                          ->select('program')
                          ->where('program IS NOT NULL')
                          ->where('program !=', '')
                          ->groupBy('program')
                          ->orderBy('program', 'ASC')
                          ->get()
                          ->getResultArray();
                           
        } catch (\Exception $e) {
            log_message('error', 'Error fetching departments/programs: ' . $e->getMessage());
            $departments = [];
            $programs = [];
        }

        return view('admin/course_edit', [
            'course' => $course,
            'departments' => $departments,
            'programs' => $programs
        ]);
    }

    public function updateCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]|max_length[150]',
            'description' => 'max_length[1000]',
            'course_code' => 'max_length[50]',
            'academic_year' => 'max_length[20]',
            'department' => 'max_length[100]',
            'program' => 'max_length[100]',
            'term' => 'max_length[20]',
            'semester' => 'max_length[50]',
            'schedule' => 'max_length[255]'
        ];

        // Custom validation for course title - no special characters allowed
        $title = $this->request->getPost('title');
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
            return redirect()->back()->withInput()->with('error', 'Course title can only contain letters, numbers, and spaces. Special characters are not allowed.');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'course_code' => $this->request->getPost('course_code'),
            'academic_year' => $this->request->getPost('academic_year'),
            'department' => $this->request->getPost('department'),
            'program' => $this->request->getPost('program'),
            'term' => $this->request->getPost('term'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->courseModel->update($id, $data)) {
            $session->setFlashdata('success', 'Course updated successfully!');
        } else {
            $session->setFlashdata('error', 'Failed to update course.');
        }

        return redirect()->to('/admin/courses');
    }

    public function deleteCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/admin/courses');
        }

        if ($this->courseModel->delete($id)) {
            $session->setFlashdata('success', 'Course deleted successfully!');
        } else {
            $session->setFlashdata('error', 'Failed to delete course.');
        }

        return redirect()->to('/admin/courses');
    }

    public function manageSchedules()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $db = \Config\Database::connect();
        
        // Get courses with assigned teachers (both directly assigned and approved assignments)
        $schedules = $db->table('courses')
                       ->select('courses.id as course_id, courses.*, users.name as teacher_name, users.email as teacher_email, courses.title as course_title, courses.course_code')
                       ->join('users', 'users.id = courses.teacher_id', 'inner')
                       ->where('courses.teacher_id IS NOT NULL')
                       ->orderBy('users.name', 'ASC')
                       ->orderBy('courses.title', 'ASC')
                       ->get()
                       ->getResultArray();

        // Also get courses with approved assignments but not yet updated in courses table
        $approvedAssignments = $db->table('teacher_assignments')
                                 ->select('teacher_assignments.*, courses.title as course_title, courses.course_code, courses.description, users.name as teacher_name, users.email as teacher_email')
                                 ->join('courses', 'courses.id = teacher_assignments.course_id')
                                 ->join('users', 'users.id = teacher_assignments.teacher_id')
                                 ->where('teacher_assignments.status', 'approved')
                                 ->where('courses.teacher_id IS NULL')
                                 ->get()
                                 ->getResultArray();

        // Merge both sets of courses
        $allSchedules = array_merge($schedules, $approvedAssignments);

        return view('admin/manage_schedules', ['schedules' => $allSchedules]);
    }

    public function updateSchedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $validation = \Config\Services::validation();
        
        // Check if this is a new assignment setup or an existing schedule update
        $teacherId = $this->request->getPost('teacher_id');
        
        if ($teacherId) {
            // New assignment setup - different validation rules
            $rules = [
                'course_id' => 'required|integer',
                'teacher_id' => 'required|integer',
                'start_day' => 'required|string',
                'end_day' => 'required|string',
                'semester' => 'required|string',
                'term' => 'required|string',
                'start_time' => 'required',
                'end_time' => 'required',
                'building' => 'required|string',
                'room_number' => 'required|string'
            ];
        } else {
            // Existing schedule update - original validation rules
            $rules = [
                'course_id' => 'required|integer',
                'day_range' => 'required|string',
                'start_time' => 'required',
                'end_time' => 'required',
                'room_number' => 'required|string',
                'building' => 'required|string',
                'room_capacity' => 'required|integer|greater_than[0]'
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        $courseId = $this->request->getPost('course_id');
        
        if ($teacherId) {
            // Handle new assignment setup
            $adminId = $session->get('id');
            
            // Get schedule data and generate day_range from start_day and end_day
            $startDay = $this->request->getPost('start_day');
            $endDay = $this->request->getPost('end_day');
            $startTime = $this->request->getPost('start_time');
            $endTime = $this->request->getPost('end_time');
            $building = $this->request->getPost('building');
            $roomNumber = $this->request->getPost('room_number');
            
            // Generate day_range from start_day and end_day
            if ($startDay && $endDay) {
                if ($startDay === $endDay) {
                    $dayRange = $startDay;
                } else {
                    $dayRange = $startDay . '-' . $endDay;
                }
            } else {
                $dayRange = '';
            }
            
            // Check for schedule conflicts with this teacher
            $db = \Config\Database::connect();
            $conflictCourses = $db->table('courses')
                                 ->select('courses.id, courses.title, courses.course_code, courses.day_range, courses.start_time, courses.end_time, courses.building, courses.room_number')
                                 ->join('teacher_assignments', 'teacher_assignments.course_id = courses.id')
                                 ->where('teacher_assignments.teacher_id', $teacherId)
                                 ->where('teacher_assignments.status', 'approved')
                                 ->where('courses.id !=', $courseId)
                                 ->where('courses.day_range IS NOT NULL')
                                 ->where('courses.start_time IS NOT NULL')
                                 ->where('courses.end_time IS NOT NULL')
                                 ->get()
                                 ->getResultArray();
            
            $hasConflict = false;
            $conflictDetails = [];
            
            foreach ($conflictCourses as $conflictCourse) {
                // Check if day ranges overlap
                if ($this->daysOverlap($dayRange, $conflictCourse['day_range'])) {
                    // Check if times overlap
                    if ($this->timesOverlap($startTime, $endTime, $conflictCourse['start_time'], $conflictCourse['end_time'])) {
                        $hasConflict = true;
                        $conflictDetails[] = [
                            'title' => $conflictCourse['title'],
                            'code' => $conflictCourse['course_code'],
                            'day_range' => $conflictCourse['day_range'],
                            'time' => date('h:i A', strtotime($conflictCourse['start_time'])) . ' - ' . date('h:i A', strtotime($conflictCourse['end_time'])),
                            'building' => $conflictCourse['building'],
                            'room' => $conflictCourse['room_number']
                        ];
                    }
                }
            }
            
            if ($hasConflict) {
                $conflictMessage = 'Schedule conflict detected! The teacher is already scheduled at this time for: ';
                foreach ($conflictDetails as $conflict) {
                    $conflictMessage .= $conflict['title'] . ' (' . $conflict['code'] . ') on ' . $conflict['day_range'] . ' ' . $conflict['time'] . ' at ' . $conflict['building'] . ' ' . $conflict['room'] . '; ';
                }
                return redirect()->back()->with('error', $conflictMessage)->withInput();
            }
            
            $scheduleData = [
                'day_range' => $dayRange,
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'room_number' => $this->request->getPost('room_number'),
                'building' => $this->request->getPost('building'),
                'semester' => $this->request->getPost('semester'),
                'term' => $this->request->getPost('term'),
                'schedule' => $dayRange . ' ' . $this->request->getPost('start_time') . '-' . $this->request->getPost('end_time'),
                'schedule_status' => 'pending', // Default to pending since relying on teacher approval
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Update the course with schedule information
            $this->courseModel->update($courseId, $scheduleData);

            // Check if assignment already exists to prevent duplicates
            $existingAssignment = $db->table('teacher_assignments')
                ->where('course_id', $courseId)
                ->where('teacher_id', $teacherId)
                ->get()
                ->getRowArray();
                
            if ($existingAssignment) {
                // Update existing assignment instead of creating duplicate
                $db->table('teacher_assignments')
                    ->where('id', $existingAssignment['id'])
                    ->update([
                        'status' => 'pending',
                        'assigned_by' => $adminId,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'responded_at' => null,
                        'response_message' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                $session->setFlashdata('success', 'Teacher assignment updated and sent for approval! The teacher will receive a notification to accept or reject the assignment.');
            } else {
                // Create new teacher assignment with approval system
                $assignmentData = [
                    'course_id' => $courseId,
                    'teacher_id' => $teacherId,
                    'status' => 'pending',
                    'assigned_by' => $adminId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($this->teacherAssignmentModel->insert($assignmentData)) {
                    $session->setFlashdata('success', 'Teacher assignment sent for approval! The teacher will receive a notification to accept or reject the assignment.');
                } else {
                    $session->setFlashdata('error', 'Failed to create teacher assignment.');
                }
            }
            
            // Clear setup session data to make form disappear
            $session->remove(['setup_course_id', 'setup_teacher_id']);

            return redirect()->to('/admin/assignments');
        } else {
            // Handle existing schedule update (original logic)
            $startDay = $this->request->getPost('start_day');
            $endDay = $this->request->getPost('end_day');
            $startTime = $this->request->getPost('start_time');
            $endTime = $this->request->getPost('end_time');
            $roomNumber = $this->request->getPost('room_number');
            $building = $this->request->getPost('building');
            $roomCapacity = $this->request->getPost('room_capacity');
            
            // Generate day_range from start_day and end_day
            if ($startDay && $endDay) {
                if ($startDay === $endDay) {
                    $dayRange = $startDay;
                } else {
                    $dayRange = $startDay . '-' . $endDay;
                }
            } else {
                $dayRange = $this->request->getPost('day_range'); // fallback for direct day_range
            }
            
            $db = \Config\Database::connect();
            
            // Get course information to find the teacher
            $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
            
            if (!$course) {
                return redirect()->back()->with('error', 'Course not found.');
            }

            // Check for schedule conflicts with this teacher (excluding current course)
            $teacherId = $course['teacher_id'];
            if ($teacherId) {
                // Debug: Log the current schedule being checked
                log_message('debug', 'Checking conflicts for teacher_id: ' . $teacherId . ', course_id: ' . $courseId);
                log_message('debug', 'Current schedule: day_range=' . $dayRange . ', start_time=' . $startTime . ', end_time=' . $endTime . ', building=' . $building . ', room=' . $roomNumber);
                
                // Simplified query - check courses directly assigned to this teacher
                $conflictCourses = $db->table('courses')
                                     ->select('courses.id, courses.title, courses.course_code, courses.day_range, courses.start_time, courses.end_time, courses.building, courses.room_number')
                                     ->where('courses.teacher_id', $teacherId)
                                     ->where('courses.id !=', $courseId)
                                     ->where('courses.day_range IS NOT NULL')
                                     ->where('courses.start_time IS NOT NULL')
                                     ->where('courses.end_time IS NOT NULL')
                                     ->get()
                                     ->getResultArray();
                
                log_message('debug', 'Found ' . count($conflictCourses) . ' other courses for this teacher');
                
                $hasConflict = false;
                $conflictDetails = [];
                
                foreach ($conflictCourses as $conflictCourse) {
                    log_message('debug', 'Checking against course: ' . $conflictCourse['title'] . ', day_range=' . $conflictCourse['day_range'] . ', time=' . $conflictCourse['start_time'] . '-' . $conflictCourse['end_time'] . ', building=' . $conflictCourse['building'] . ', room=' . $conflictCourse['room_number']);
                    
                    // Check if day ranges overlap
                    $daysOverlap = $this->daysOverlap($dayRange, $conflictCourse['day_range']);
                    log_message('debug', 'Days overlap: ' . ($daysOverlap ? 'YES' : 'NO'));
                    
                    if ($daysOverlap) {
                        // Check if times overlap
                        $timesOverlap = $this->timesOverlap($startTime, $endTime, $conflictCourse['start_time'], $conflictCourse['end_time']);
                        log_message('debug', 'Times overlap: ' . ($timesOverlap ? 'YES' : 'NO'));
                        
                        if ($timesOverlap) {
                            $hasConflict = true;
                            $conflictDetails[] = [
                                'title' => $conflictCourse['title'],
                                'code' => $conflictCourse['course_code'],
                                'day_range' => $conflictCourse['day_range'],
                                'time' => date('h:i A', strtotime($conflictCourse['start_time'])) . ' - ' . date('h:i A', strtotime($conflictCourse['end_time'])),
                                'building' => $conflictCourse['building'],
                                'room' => $conflictCourse['room_number']
                            ];
                            log_message('debug', 'CONFLICT DETECTED!');
                        }
                    }
                }
                
                if ($hasConflict) {
                    $conflictMessage = 'Schedule conflict detected! The teacher is already scheduled at this time for: ';
                    foreach ($conflictDetails as $conflict) {
                        $conflictMessage .= $conflict['title'] . ' (' . $conflict['code'] . ') on ' . $conflict['day_range'] . ' ' . $conflict['time'] . ' at ' . $conflict['building'] . ' ' . $conflict['room'] . '; ';
                    }
                    log_message('debug', 'Conflict message: ' . $conflictMessage);
                    return redirect()->back()->with('error', $conflictMessage)->withInput();
                } else {
                    log_message('debug', 'No conflicts found, proceeding with update');
                }
            } else {
                log_message('debug', 'No teacher_id found for course: ' . $courseId);
            }

            // Update course schedule information
            $updateData = [
                'day_range' => $dayRange,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'room_number' => $roomNumber,
                'building' => $building,
                'room_capacity' => $roomCapacity,
                'schedule' => $dayRange . ' ' . $startTime . '-' . $endTime,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->courseModel->update($courseId, $updateData)) {
                $session->setFlashdata('success', 'Schedule updated successfully!');
            } else {
                $session->setFlashdata('error', 'Failed to update schedule.');
            }

            return redirect()->to('/admin/schedules');
        }
    }

    public function deleteSchedule($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $db = \Config\Database::connect();
        
        // Get course information to find the teacher
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$course || !$course['teacher_id']) {
            return redirect()->back()->with('error', 'Course or teacher not found.');
        }
        
        $teacherId = $course['teacher_id'];
        
        // Clear schedule information from the course
        $updateData = [
            'day_range' => null,
            'start_time' => null,
            'end_time' => null,
            'room_number' => null,
            'building' => null,
            'room_capacity' => null,
            'schedule_status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->courseModel->update($courseId, $updateData)) {
            $session->setFlashdata('success', 'Schedule deleted successfully!');
        } else {
            $session->setFlashdata('error', 'Failed to delete schedule.');
        }

        return redirect()->to('/admin/schedules');
    }

    public function enrollments()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $db = \Config\Database::connect();
        
        // Get courses with teacher information and schedule details
        $courses = $db->table('courses')
                      ->select('courses.*, users.name as teacher_name, courses.day_range, courses.start_time, courses.end_time, courses.room_number, courses.building, courses.schedule_status')
                      ->join('users', 'users.id = courses.teacher_id', 'left')
                      ->orderBy('courses.title', 'ASC')
                      ->get()
                      ->getResultArray();

        // Add enrollment count and schedule information for each course
        foreach ($courses as &$course) {
            // Get enrollment count from main enrollments table
            $enrollmentCount = $db->table('enrollments')
                                  ->where('course_id', $course['id'])
                                  ->countAllResults();
            
            // Get approved enrollments from student_enrollments table
            $approvedEnrollmentCount = $db->table('student_enrollments')
                                         ->where('course_id', $course['id'])
                                         ->where('status', 'approved')
                                         ->countAllResults();
            
            $course['enrollment_count'] = $enrollmentCount + $approvedEnrollmentCount;
            
            // Format schedule information if available
            if ($course['day_range'] && $course['start_time'] && $course['end_time']) {
                $course['schedule_info'] = $course['day_range'] . ' ' . 
                    date('h:i A', strtotime($course['start_time'])) . ' - ' . 
                    date('h:i A', strtotime($course['end_time']));
                
                if ($course['room_number'] && $course['building']) {
                    $course['schedule_info'] .= ' | Room: ' . $course['room_number'] . ', ' . $course['building'];
                }
            } else {
                $course['schedule_info'] = 'No schedule set';
            }
        }

        return view('admin/enrollments', ['courses' => $courses]);
    }

    public function viewEnrollmentDetails($courseId = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $db = \Config\Database::connect();
        
        // Get course information with teacher details
        $course = $db->table('courses')
                     ->select('courses.*, users.name as teacher_name')
                     ->join('users', 'users.id = courses.teacher_id', 'left')
                     ->where('courses.id', $courseId)
                     ->get()
                     ->getRowArray();
        if (!$course) {
            return redirect()->to('/admin/enrollments')->with('error', 'Course not found.');
        }

        // Get currently enrolled students with enrollment details
        $enrolledStudents = $db->table('enrollments')
                               ->select('enrollments.*, users.name, users.email, enrollments.enrolled_at')
                               ->join('users', 'users.id = enrollments.user_id')
                               ->where('enrollments.course_id', $courseId)
                               ->orderBy('enrollments.enrolled_at', 'DESC')
                               ->get()
                               ->getResultArray();

        return view('admin/view_enrollments', [
            'course' => $course,
            'enrolled_students' => $enrolledStudents
        ]);
    }

    public function enrollStudent($courseId = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $db = \Config\Database::connect();
        
        // Get course information
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$course) {
            return redirect()->to('/admin/enrollments')->with('error', 'Course not found.');
        }

        // Get students (users with role 'student')
        $students = $db->table('users')
                      ->where('role', 'student')
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();

        // Get currently enrolled students with enrollment id
        $enrolledStudents = $db->table('student_enrollments')
                               ->select('student_enrollments.id as enrollment_id, users.name, users.email, student_enrollments.enrolled_at')
                               ->join('users', 'users.id = student_enrollments.student_id')
                               ->where('student_enrollments.course_id', $courseId)
                               ->orderBy('student_enrollments.enrolled_at', 'DESC')
                               ->get()
                               ->getResultArray();

        return view('admin/enroll_student', [
            'course' => $course,
            'students' => $students,
            'enrolled_students' => $enrolledStudents
        ]);
    }

    public function assignTeacher($courseId = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $db = \Config\Database::connect();
        
        // Get course information
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$course) {
            return redirect()->to('/admin/enrollments')->with('error', 'Course not found.');
        }

        // Get teachers (users with role 'teacher')
        $teachers = $db->table('users')
                      ->where('role', 'teacher')
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();

        // Get current teacher if assigned
        $currentTeacher = null;
        if ($course['teacher_id']) {
            $currentTeacher = $db->table('users')->where('id', $course['teacher_id'])->get()->getRowArray();
        }

        return view('admin/assign_teacher', [
            'course' => $course,
            'teachers' => $teachers,
            'current_teacher' => $currentTeacher
        ]);
    }

    public function storeEnrollment()
    {
        $session = session();
        
        // Debug: Log session and request data
        log_message('debug', 'Admin storeEnrollment: Session data: ' . json_encode($session->get()));
        log_message('debug', 'Admin storeEnrollment: Request data: ' . json_encode($this->request->getPost()));
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Admin storeEnrollment: Validation errors: ' . json_encode($validation->getErrors()));
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        $courseId = $this->request->getPost('course_id');
        $studentId = $this->request->getPost('user_id');
        $enrolledBy = $session->get('id');

        $db = \Config\Database::connect();
        
        // Check if student is already enrolled in this course (in either table)
        log_message('debug', 'Admin storeEnrollment: Checking existing enrollment for course_id: ' . $courseId . ', student_id: ' . $studentId);
        
        $existingEnrollment = $db->table('student_enrollments')
                                 ->where('course_id', $courseId)
                                 ->where('student_id', $studentId)
                                 ->get()
                                 ->getRowArray();

        log_message('debug', 'Admin storeEnrollment: Existing enrollment found: ' . ($existingEnrollment ? 'Yes' : 'No'));

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'Student is already enrolled in this course.');
        }

        $existingMainEnrollment = $db->table('enrollments')
                                     ->where('course_id', $courseId)
                                     ->where('user_id', $studentId)
                                     ->get()
                                     ->getRowArray();

        if ($existingMainEnrollment) {
            return redirect()->back()->with('error', 'Student is already enrolled in this course.');
        }

        // Get course information for semester and term
        log_message('debug', 'Admin storeEnrollment: Getting course info for course_id: ' . $courseId);
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        
        if (!$course) {
            log_message('error', 'Admin storeEnrollment: Course not found with ID: ' . $courseId);
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Create student enrollment with approval system
        $enrollmentData = [
            'course_id' => $courseId,
            'student_id' => $studentId,
            'status' => 'pending',
            'enrolled_by' => $enrolledBy,
            'enrolled_at' => date('Y-m-d H:i:s'),
            'semester' => $course['semester'] ?? 'Not specified',
            'term' => $course['term'] ?? 'Not specified',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Debug: Log the enrollment data
        log_message('debug', 'Admin storeEnrollment: Attempting to insert enrollment data: ' . json_encode($enrollmentData));
        
        try {
            // Use database directly to avoid potential model timestamp conflicts
            $insertResult = $db->table('student_enrollments')->insert($enrollmentData);
            
            if ($insertResult) {
                log_message('debug', 'Admin storeEnrollment: Successfully inserted enrollment');
                $session->setFlashdata('success', 'Student enrollment created successfully! The student will receive a notification to accept or reject the enrollment.');
            } else {
                log_message('error', 'Admin storeEnrollment: Failed to insert enrollment - no error returned but insert failed');
                $session->setFlashdata('error', 'Failed to create student enrollment.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Admin storeEnrollment: Database error: ' . $e->getMessage());
            $session->setFlashdata('error', 'Database error: ' . $e->getMessage());
        }

        return redirect()->to('/admin/student-enrollments');
    }

    public function studentEnrollments($status = 'all')
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        
        // Get enrollment requests with filtering
        $enrollments = $db->table('student_enrollments se')
            ->select('se.*, c.course_code, c.title as course_title, c.semester as course_semester, c.term as course_term, u.name as student_name, u.email as student_email, enroller.name as enrolled_by_name')
            ->join('courses c', 'c.id = se.course_id')
            ->join('users u', 'u.id = se.student_id')
            ->join('users enroller', 'enroller.id = se.enrolled_by', 'left')
            ->orderBy('se.enrolled_at', 'DESC');
            
        if ($status !== 'all') {
            $enrollments->where('se.status', $status);
        }
        
        $enrollments = $enrollments->get()->getResultArray();
        
        // Get counts for each status
        $counts = [
            'all' => $db->table('student_enrollments')->countAllResults(),
            'pending' => $db->table('student_enrollments')->where('status', 'pending')->countAllResults(),
            'approved' => $db->table('student_enrollments')->where('status', 'approved')->countAllResults(),
            'rejected' => $db->table('student_enrollments')->where('status', 'rejected')->countAllResults()
        ];

        return view('admin/student_enrollments', [
            'enrollments' => $enrollments,
            'currentStatus' => $status,
            'counts' => $counts
        ]);
    }

    public function approveStudentEnrollment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            $db = \Config\Database::connect();
            
            // Update enrollment status to approved
            $db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'approved',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Enrollment approved by admin',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
            $session->setFlashdata('success', 'Student enrollment approved successfully!');
            
        } catch (\Throwable $e) {
            log_message('error', 'Approve enrollment error: {err}', ['err' => $e->getMessage()]);
            $session->setFlashdata('error', 'Failed to approve enrollment');
        }

        return redirect()->to('/admin/student-enrollments/pending');
    }

    public function rejectStudentEnrollment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $enrollmentId = $this->request->getPost('enrollment_id');
        
        try {
            $db = \Config\Database::connect();
            
            // Update enrollment status to rejected
            $db->table('student_enrollments')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'rejected',
                    'responded_at' => date('Y-m-d H:i:s'),
                    'response_message' => 'Enrollment rejected by admin',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
            $session->setFlashdata('success', 'Student enrollment rejected successfully!');
            
        } catch (\Throwable $e) {
            log_message('error', 'Reject enrollment error: {err}', ['err' => $e->getMessage()]);
            $session->setFlashdata('error', 'Failed to reject enrollment');
        }

        return redirect()->to('/admin/student-enrollments/pending');
    }

    public function removeEnrollment($enrollmentId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        $db = \Config\Database::connect();
        
        // Get enrollment details
        $enrollment = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        if (!$enrollment) {
            return redirect()->to('/admin/enrollments')->with('error', 'Enrollment not found.');
        }

        // Remove enrollment
        $db->table('enrollments')->where('id', $enrollmentId)->delete();

        return redirect()->to('/admin/enrollments/course/' . $enrollment['course_id'])->with('success', 'Student removed from course successfully!');
    }

    public function storeTeacherAssignment($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'teacher_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        $teacherId = $this->request->getPost('teacher_id');
        $adminId = $session->get('id');
        
        // Create assignment record in teacher_assignments table
        $db = \Config\Database::connect();
        
        try {
            // Store the selected teacher in session for the manage schedules page
            $session->set('setup_course_id', $courseId);
            $session->set('setup_teacher_id', $teacherId);
            
            // Redirect to Manage Schedules page to configure schedule before approval
            return redirect()->to('/admin/schedules')->with('success', 'Teacher selected. Please configure schedule details before sending for approval.');
            
        } catch (\Throwable $e) {
            log_message('error', 'Teacher selection error: {err}', ['err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to select teacher');
        }
    }

    private function daysOverlap($day1, $day2)
    {
        // Convert abbreviated days to full days and expand single letters
        $dayMap = [
            'M' => 'Monday', 'T' => 'Tuesday', 'W' => 'Wednesday', 
            'Th' => 'Thursday', 'F' => 'Friday', 'S' => 'Saturday',
            'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday',
            'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday'
        ];
        
        // Expand abbreviations like "MWF" to individual days
        $expandDays = function($dayStr) use ($dayMap) {
            $days = [];
            $parts = array_map('trim', explode('-', $dayStr));
            
            foreach ($parts as $part) {
                // Handle single letter abbreviations (like MWF)
                if (strlen($part) === 1) {
                    $letters = str_split($part);
                    foreach ($letters as $letter) {
                        $fullDay = $dayMap[$letter] ?? null;
                        if ($fullDay && !in_array($fullDay, $days)) {
                            $days[] = $fullDay;
                        }
                    }
                } else {
                    // Handle full day names or multi-letter abbreviations
                    $fullDay = $dayMap[$part] ?? $part;
                    if (!in_array($fullDay, $days)) {
                        $days[] = $fullDay;
                    }
                }
            }
            
            return $days;
        };
        
        $days1 = $expandDays($day1);
        $days2 = $expandDays($day2);
        
        // Check if any day overlaps
        foreach ($days1 as $day1) {
            if (in_array($day1, $days2)) {
                return true;
            }
        }
        
        return false;
    }

    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        $start1Time = strtotime($start1);
        $end1Time = strtotime($end1);
        $start2Time = strtotime($start2);
        $end2Time = strtotime($end2);
        
        return !($end1Time <= $start2Time || $end2Time <= $start1Time);
    }

    public function viewAssignments()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Admin privileges required.');
        }

        $status = $this->request->getGet('status') ?? 'all';
        $db = \Config\Database::connect();
        
        // First try the full query with joins
        $query = $db->table('teacher_assignments')
                   ->select('teacher_assignments.*, courses.title as course_title, courses.course_code, courses.day_range, courses.start_time, courses.end_time, courses.building, courses.room_number, users.name as teacher_name, users.email as teacher_email')
                   ->join('courses', 'courses.id = teacher_assignments.course_id', 'left')
                   ->join('users', 'users.id = teacher_assignments.teacher_id', 'left')
                   ->orderBy('teacher_assignments.created_at', 'DESC');
        
        if ($status !== 'all') {
            $query->where('teacher_assignments.status', $status);
        }
        
        $assignments = $query->get()->getResultArray();
        
        // If no results, try a simpler query to see if assignments exist
        if (empty($assignments)) {
            $simpleQuery = $db->table('teacher_assignments');
            if ($status !== 'all') {
                $simpleQuery->where('status', $status);
            }
            $simpleAssignments = $simpleQuery->get()->getResultArray();
            
            if (!empty($simpleAssignments)) {
                // Assignments exist but joins are failing, get basic info
                foreach ($simpleAssignments as &$assignment) {
                    $assignment['course_title'] = 'Course #' . $assignment['course_id'];
                    $assignment['course_code'] = 'N/A';
                    $assignment['teacher_name'] = 'Teacher #' . $assignment['teacher_id'];
                    $assignment['teacher_email'] = 'N/A';
                    $assignment['day_range'] = null;
                    $assignment['start_time'] = null;
                    $assignment['end_time'] = null;
                    $assignment['building'] = null;
                    $assignment['room_number'] = null;
                }
                $assignments = $simpleAssignments;
            }
        }
        
        return view('admin/view_assignments', [
            'assignments' => $assignments,
            'currentStatus' => $status
        ]);
    }

    public function getAssignmentDetails($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $assignment = $db->table('teacher_assignments')
                        ->select('teacher_assignments.*, courses.title as course_title, courses.course_code, courses.day_range, courses.start_time, courses.end_time, courses.building, courses.room_number, users.name as teacher_name, users.email as teacher_email')
                        ->join('courses', 'courses.id = teacher_assignments.course_id')
                        ->join('users', 'users.id = teacher_assignments.teacher_id')
                        ->where('teacher_assignments.id', $id)
                        ->get()
                        ->getRowArray();

        if ($assignment) {
            // Format times for display
            if ($assignment['start_time']) {
                $assignment['start_time'] = date('h:i A', strtotime($assignment['start_time']));
            }
            if ($assignment['end_time']) {
                $assignment['end_time'] = date('h:i A', strtotime($assignment['end_time']));
            }
            if ($assignment['assigned_at']) {
                $assignment['assigned_at'] = date('M d, Y H:i', strtotime($assignment['assigned_at']));
            }
            if ($assignment['responded_at']) {
                $assignment['responded_at'] = date('M d, Y H:i', strtotime($assignment['responded_at']));
            }
            
            return $this->response->setJSON(['success' => true, 'data' => $assignment]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }
    }

    public function cancelAssignmentById($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get assignment details before canceling
            $assignment = $db->table('teacher_assignments')->where('id', $id)->get()->getRowArray();
            
            if (!$assignment) {
                return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
            }

            // Delete the assignment
            $db->table('teacher_assignments')->where('id', $id)->delete();
            
            // Clear course schedule if this was the only assignment
            $otherAssignments = $db->table('teacher_assignments')
                                  ->where('course_id', $assignment['course_id'])
                                  ->where('status', 'approved')
                                  ->countAllResults();
            
            if ($otherAssignments == 0) {
                $db->table('courses')->where('id', $assignment['course_id'])->update([
                    'teacher_id' => null,
                    'schedule_status' => 'inactive',
                    'day_range' => null,
                    'start_time' => null,
                    'end_time' => null,
                    'room_number' => null,
                    'building' => null
                ]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Assignment canceled successfully']);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function cancelAssignment()
    {
        $session = session();
        
        // Get the setup data before clearing it
        $courseId = $session->get('setup_course_id');
        $teacherId = $session->get('setup_teacher_id');
        
        // Clear the session data to make form disappear
        $session->remove(['setup_course_id', 'setup_teacher_id']);
        
        // If we have course and teacher IDs, remove any pending assignment
        if ($courseId && $teacherId) {
            try {
                // Remove any pending teacher assignment for this course and teacher
                $this->teacherAssignmentModel->where([
                    'course_id' => $courseId,
                    'teacher_id' => $teacherId,
                    'status' => 'pending'
                ])->delete();
                
                $session->setFlashdata('success', 'Teacher assignment cancelled successfully.');
            } catch (\Throwable $e) {
                log_message('error', 'Cancel assignment error: {err}', ['err' => $e->getMessage()]);
                $session->setFlashdata('error', 'Failed to cancel assignment, but form has been cleared.');
            }
        }
        
        // Redirect back to schedules page
        return redirect()->to('/admin/schedules');
    }
}
