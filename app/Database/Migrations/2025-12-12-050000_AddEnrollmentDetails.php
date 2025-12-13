<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentDetails extends Migration
{
    public function up()
    {
        // Add semester, term, and schedule fields to enrollments table
        $this->forge->addColumn('enrollments', [
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'course_id'
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'semester'
            ],
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'term'
            ]
        ]);
    }

    public function down()
    {
        // Drop the columns
        $this->forge->dropColumn('enrollments', ['semester', 'term', 'schedule']);
    }
}
