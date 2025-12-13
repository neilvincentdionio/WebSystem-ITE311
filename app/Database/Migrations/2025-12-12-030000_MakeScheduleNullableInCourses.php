<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeScheduleNullableInCourses extends Migration
{
    public function up()
    {
        // Make schedule field nullable
        $this->forge->modifyColumn('courses', [
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        // Revert back to not nullable if needed
        $this->forge->modifyColumn('courses', [
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ]
        ]);
    }
}
