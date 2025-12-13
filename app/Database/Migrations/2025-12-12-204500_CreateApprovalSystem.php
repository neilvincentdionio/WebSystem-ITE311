<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalSystem extends Migration
{
    public function up()
    {
        // Teacher Assignments Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'response_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['course_id', 'teacher_id']);
        $this->forge->addKey('status');
        $this->forge->addKey('teacher_id');
        $this->forge->createTable('teacher_assignments');

        // Student Enrollments Table (for approval system)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'enrolled_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'enrolled_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'response_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['course_id', 'student_id']);
        $this->forge->addKey('status');
        $this->forge->addKey('student_id');
        $this->forge->createTable('student_enrollments');
    }

    public function down()
    {
        $this->forge->dropTable('teacher_assignments');
        $this->forge->dropTable('student_enrollments');
    }
}
