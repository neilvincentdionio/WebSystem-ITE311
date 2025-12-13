<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGradingWeightsTable extends Migration
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
            'weight_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
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
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('grading_period_id', 'grading_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assignment_type_id', 'assignment_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['course_id', 'grading_period_id', 'assignment_type_id']);
        $this->forge->createTable('grading_weights');
    }

    public function down()
    {
        $this->forge->dropTable('grading_weights');
    }
}
