<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailedScheduleFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'day_range' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Day range e.g., Monday-Wednesday'
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class start time'
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class end time'
            ],
            'room_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Room number'
            ],
            'building' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Building name'
            ],
            'room_capacity' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'Room capacity'
            ],
            'schedule_status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'pending'],
                'default' => 'active',
                'comment' => 'Schedule status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'day_range',
            'start_time',
            'end_time',
            'room_number',
            'building',
            'room_capacity',
            'schedule_status'
        ]);
    }
}
