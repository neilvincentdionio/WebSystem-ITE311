<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentRequestFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'approved',
                'after' => 'enrolled_at'
            ],
            'response_message' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'response_message'
            ],
            'enrolled_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'responded_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['status', 'response_message', 'responded_at', 'enrolled_by']);
    }
}
