<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at', 'status', 'response_message', 'responded_at', 'enrolled_by'];

    // Automatically manage timestamps (optional)
    protected $useTimestamps = false;

    /**
     * Create enrollment request
     *
     * @param array $data
     * @return bool|int  Insert ID or false if failed
     */
    public function createRequest($data)
    {
        // Check if already enrolled or has pending request
        if ($this->hasExistingRequest($data['user_id'], $data['course_id'])) {
            return false;
        }

        return $this->insert($data);
    }

    /**
     * Get pending enrollment requests for teacher
     *
     * @param int $teacher_id
     * @return array
     */
    public function getPendingEnrollments($teacher_id)
    {
        return $this->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.course_code')
                    ->from('enrollments e')
                    ->join('users u', 'u.id = e.user_id')
                    ->join('courses c', 'c.id = e.course_id')
                    ->where('c.teacher_id', $teacher_id)
                    ->where('e.status', 'pending')
                    ->orderBy('e.enrolled_at', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get all enrollment requests for teacher (pending, approved, rejected)
     *
     * @param int $teacher_id
     * @param string $status
     * @return array
     */
    public function getEnrollmentRequests($teacher_id, $status = 'all')
    {
        $query = $this->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.course_code')
                    ->from('enrollments e')
                    ->join('users u', 'u.id = e.user_id')
                    ->join('courses c', 'c.id = e.course_id')
                    ->where('c.teacher_id', $teacher_id);
        
        if ($status !== 'all') {
            $query->where('e.status', $status);
        }
        
        return $query->orderBy('e.enrolled_at', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Update enrollment request status
     *
     * @param int $enrollment_id
     * @param string $status
     * @param string $message
     * @return bool
     */
    public function updateStatus($enrollment_id, $status, $message = null)
    {
        $data = [
            'status' => $status,
            'responded_at' => date('Y-m-d H:i:s')
        ];
        
        if ($message) {
            $data['response_message'] = $message;
        }
        
        return $this->update($enrollment_id, $data);
    }

    /**
     * Check if user has existing enrollment or request
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    public function hasExistingRequest($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->countAllResults() > 0;
    }

    /**
     * Enroll a user in a course (legacy method)
     *
     * @param array $data
     * @return bool|int  Insert ID or false if failed
     */
    public function enrollUser($data)
    {
        // Check if already enrolled before inserting
        if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'])) {
            return false;
        }

        return $this->insert($data);
    }

    /**
     * Get all courses a specific user is enrolled in
     *
     * @param int $user_id
     * @return array
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('courses.*')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'approved')
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->where('status', 'approved')
                    ->countAllResults() > 0;
    }
}
