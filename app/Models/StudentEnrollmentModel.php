<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentEnrollmentModel extends Model
{
    protected $table = 'student_enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id',
        'student_id',
        'status',
        'enrolled_by',
        'enrolled_at',
        'responded_at',
        'response_message',
        'semester',
        'term',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    public function getPendingEnrollments($studentId = null)
    {
        $builder = $this->builder();
        $builder->select('student_enrollments.*, courses.title as course_title, courses.course_code, users.name as student_name');
        $builder->join('courses', 'courses.id = student_enrollments.course_id');
        $builder->join('users', 'users.id = student_enrollments.student_id');
        $builder->where('student_enrollments.status', 'pending');
        
        if ($studentId) {
            $builder->where('student_enrollments.student_id', $studentId);
        }
        
        return $builder->get()->getResultArray();
    }

    public function getEnrollmentDetails($id)
    {
        $builder = $this->builder();
        $builder->select('student_enrollments.*, courses.title as course_title, courses.course_code, courses.description, courses.units, users.name as student_name, users.email as student_email, enroller.name as enrolled_by_name');
        $builder->join('courses', 'courses.id = student_enrollments.course_id');
        $builder->join('users', 'users.id = student_enrollments.student_id');
        $builder->join('users as enroller', 'enroller.id = student_enrollments.enrolled_by', 'left');
        $builder->where('student_enrollments.id', $id);
        
        return $builder->get()->getRowArray();
    }

    public function getStudentEnrollments($studentId)
    {
        $builder = $this->builder();
        $builder->select('student_enrollments.*, courses.title as course_title, courses.course_code, courses.description, courses.units');
        $builder->join('courses', 'courses.id = student_enrollments.course_id');
        $builder->where('student_enrollments.student_id', $studentId);
        $builder->orderBy('student_enrollments.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getCourseEnrollments($courseId)
    {
        $builder = $this->builder();
        $builder->select('student_enrollments.*, users.name as student_name, users.email as student_email');
        $builder->join('users', 'users.id = student_enrollments.student_id');
        $builder->where('student_enrollments.course_id', $courseId);
        $builder->orderBy('student_enrollments.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function respondToEnrollment($id, $status, $message = null)
    {
        $data = [
            'status' => $status,
            'responded_at' => date('Y-m-d H:i:s'),
        ];
        
        if ($message) {
            $data['response_message'] = $message;
        }
        
        return $this->update($id, $data);
    }

    public function getEnrollmentStats()
    {
        $stats = [];
        
        // Total enrollments
        $stats['total'] = $this->countAllResults();
        
        // Pending enrollments
        $stats['pending'] = $this->where('status', 'pending')->countAllResults();
        
        // Approved enrollments
        $stats['approved'] = $this->where('status', 'approved')->countAllResults();
        
        // Rejected enrollments
        $stats['rejected'] = $this->where('status', 'rejected')->countAllResults();
        
        return $stats;
    }

    public function getStudentApprovedCourses($studentId)
    {
        $builder = $this->builder();
        $builder->select('student_enrollments.*, courses.title as course_title, courses.course_code, courses.description, courses.units, courses.schedule');
        $builder->join('courses', 'courses.id = student_enrollments.course_id');
        $builder->where('student_enrollments.student_id', $studentId);
        $builder->where('student_enrollments.status', 'approved');
        $builder->orderBy('courses.title', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if student is already enrolled in a course
     */
    public function isAlreadyEnrolled($studentId, $courseId)
    {
        return $this->where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->countAllResults() > 0;
    }
}
