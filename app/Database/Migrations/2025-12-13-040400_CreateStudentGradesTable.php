<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentGradesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'grading_period_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'assignment_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false
            ],
            'percentage_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Computed as (score / max_score) * 100'
            ],
            'weighted_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Computed as percentage_score * weight_percentage'
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'graded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Teacher who graded this'
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('grading_period_id', 'grading_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assignment_type_id', 'assignment_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('graded_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['student_id', 'course_id', 'grading_period_id', 'assignment_type_id']);
        $this->forge->createTable('student_grades');
    }

    public function down()
    {
        $this->forge->dropTable('student_grades');
    }
}
