<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExamTypeToMaterials extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'exam_type' => [
                'type' => 'ENUM',
                'constraint' => ['prelim', 'midterm', 'finals'],
                'null' => true,
                'after' => 'course_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('materials', 'exam_type');
    }
}
