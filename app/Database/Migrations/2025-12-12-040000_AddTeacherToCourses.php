<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherToCourses extends Migration
{
    public function up()
    {
        // Add teacher_id field to courses table
        $this->forge->addColumn('courses', [
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ]
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE courses DROP FOREIGN KEY courses_teacher_id_foreign');
        
        // Drop the column
        $this->forge->dropColumn('courses', 'teacher_id');
    }
}
