<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'instructor_id', 'created_at', 'updated_at'];
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
}

