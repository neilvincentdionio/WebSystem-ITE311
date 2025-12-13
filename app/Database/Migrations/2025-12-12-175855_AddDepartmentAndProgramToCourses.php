<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartmentAndProgramToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'academic_year'
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'department'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'department');
        $this->forge->dropColumn('courses', 'program');
    }
}
