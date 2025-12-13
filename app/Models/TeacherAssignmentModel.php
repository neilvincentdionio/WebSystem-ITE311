<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherAssignmentModel extends Model
{
    protected $table = 'teacher_assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id',
        'teacher_id', 
        'status',
        'assigned_by',
        'assigned_at',
        'responded_at',
        'response_message',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    public function getRecentAssignments($limit = 5)
    {
        $builder = $this->builder();
        $builder->select('teacher_assignments.*, courses.title as course_title, courses.course_code, users.name as teacher_name');
        $builder->join('courses', 'courses.id = teacher_assignments.course_id');
        $builder->join('users', 'users.id = teacher_assignments.teacher_id');
        $builder->whereIn('teacher_assignments.status', ['pending', 'approved']);
        $builder->orderBy('teacher_assignments.created_at', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    public function getPendingAssignments($teacherId = null)
    {
        $builder = $this->builder();
        $builder->select('teacher_assignments.*, courses.title as course_title, courses.course_code, users.name as teacher_name');
        $builder->join('courses', 'courses.id = teacher_assignments.course_id');
        $builder->join('users', 'users.id = teacher_assignments.teacher_id');
        $builder->where('teacher_assignments.status', 'pending');
        
        if ($teacherId) {
            $builder->where('teacher_assignments.teacher_id', $teacherId);
        }
        
        return $builder->get()->getResultArray();
    }

    public function getAssignmentDetails($id)
    {
        $builder = $this->builder();
        $builder->select('teacher_assignments.*, courses.title as course_title, courses.course_code, courses.description, users.name as teacher_name, users.email as teacher_email, assigner.name as assigned_by_name');
        $builder->join('courses', 'courses.id = teacher_assignments.course_id');
        $builder->join('users', 'users.id = teacher_assignments.teacher_id');
        $builder->join('users as assigner', 'assigner.id = teacher_assignments.assigned_by', 'left');
        $builder->where('teacher_assignments.id', $id);
        
        return $builder->get()->getRowArray();
    }

    public function getTeacherAssignments($teacherId)
    {
        $builder = $this->builder();
        $builder->select('teacher_assignments.*, courses.title as course_title, courses.course_code, courses.description');
        $builder->join('courses', 'courses.id = teacher_assignments.course_id');
        $builder->where('teacher_assignments.teacher_id', $teacherId);
        $builder->orderBy('teacher_assignments.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getCourseAssignment($courseId)
    {
        $builder = $this->builder();
        $builder->select('teacher_assignments.*, users.name as teacher_name, users.email as teacher_email');
        $builder->join('users', 'users.id = teacher_assignments.teacher_id');
        $builder->where('teacher_assignments.course_id', $courseId);
        $builder->orderBy('teacher_assignments.created_at', 'DESC');
        
        return $builder->get()->getRowArray();
    }

    public function respondToAssignment($id, $status, $message = null)
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

    public function getAssignmentStats()
    {
        $stats = [];
        
        // Total assignments
        $stats['total'] = $this->countAllResults();
        
        // Pending assignments
        $stats['pending'] = $this->where('status', 'pending')->countAllResults();
        
        // Approved assignments
        $stats['approved'] = $this->where('status', 'approved')->countAllResults();
        
        // Rejected assignments
        $stats['rejected'] = $this->where('status', 'rejected')->countAllResults();
        
        return $stats;
    }
}
