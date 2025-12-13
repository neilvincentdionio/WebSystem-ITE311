<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'course_code', 'description', 'academic_year', 
        'department', 'program', 'term', 'semester', 'units', 'schedule', 
        'teacher_id', 'day_range', 'start_time', 'end_time', 'room_number', 
        'building', 'room_capacity', 'schedule_status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Get all courses
     *
     * @return array
     */
    public function getAllCourses()
    {
        return $this->findAll();
    }

    /**
     * Get course by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getCourseById($id)
    {
        return $this->find($id);
    }

    /**
     * Check if course should be marked as completed
     * A course should be completed when:
     * 1. The course has ended (end_date < current date)
     * 2. There are enrolled students in that course
     *
     * @param int $courseId
     * @return bool
     */
    public function shouldMarkAsCompleted($courseId)
    {
        $course = $this->find($courseId);
        if (!$course || $course['status'] === 'Completed') {
            return false;
        }

        // Check if course has ended
        if (!$course['end_date'] || $course['end_date'] > date('Y-m-d')) {
            return false;
        }

        // Check if there are enrolled students
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrolledCount = $enrollmentModel->where('course_id', $courseId)->countAll();

        return $enrolledCount > 0;
    }

    /**
     * Auto-update course status to Completed if conditions are met
     *
     * @param int $courseId
     * @return bool
     */
    public function autoUpdateStatus($courseId)
    {
        if ($this->shouldMarkAsCompleted($courseId)) {
            return $this->update($courseId, ['status' => 'Completed']);
        }
        return false;
    }

    /**
     * Update all eligible courses to Completed status
     * This can be called via cron job or scheduled task
     *
     * @return int Number of courses updated
     */
    public function updateAllCompletedCourses()
    {
        $builder = $this->builder();
        $builder->select('courses.id')
                ->join('enrollments', 'enrollments.course_id = courses.id', 'inner')
                ->where('courses.status', 'Active')
                ->where('courses.end_date <', date('Y-m-d'))
                ->groupBy('courses.id');

        $coursesToUpdate = $builder->get()->getResultArray();
        
        $updatedCount = 0;
        foreach ($coursesToUpdate as $course) {
            if ($this->update($course['id'], ['status' => 'Completed'])) {
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Model event handler for afterUpdate
     * Automatically checks and updates course status
     */
    protected function afterUpdate(array $data)
    {
        // Auto-update status if conditions are met
        $this->autoUpdateStatus($data['id'][$this->primaryKey] ?? $data['id']);
        return parent::afterUpdate($data);
    }
}

