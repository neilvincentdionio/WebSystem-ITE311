<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at'];

    // Automatically manage timestamps (optional)
    protected $useTimestamps = false;

    /**
     * Enroll a user in a course
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
                    ->countAllResults() > 0;
    }
}
