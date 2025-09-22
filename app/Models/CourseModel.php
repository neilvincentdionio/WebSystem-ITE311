<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';   // Make sure your DB has a `courses` table
    protected $primaryKey = 'id';

    // Fields that are allowed to be inserted/updated
    protected $allowedFields = ['title', 'description', 'created_by', 'created_at', 'updated_at'];

    // Automatically manage created_at & updated_at fields
    protected $useTimestamps = true;

    protected $returnType    = 'array';
}
