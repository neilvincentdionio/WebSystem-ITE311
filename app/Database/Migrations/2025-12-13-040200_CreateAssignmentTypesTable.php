<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentTypesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => '100.00'
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true
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
        $this->forge->createTable('assignment_types');

        // Insert default assignment types
        $this->db->table('assignment_types')->insertBatch([
            [
                'name' => 'Assignments',
                'code' => 'ASSIGN',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Attendance',
                'code' => 'ATTEND',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Examinations',
                'code' => 'EXAM',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Laboratory',
                'code' => 'LAB',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Quizzes',
                'code' => 'QUIZ',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Recitation',
                'code' => 'RECIT',
                'max_score' => 100.00,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('assignment_types');
    }
}
