<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGradingPeriodsTable extends Migration
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
                'constraint' => 50,
                'null' => false
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'academic_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['first', 'second', 'summer'],
                'null' => false
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => false
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
        $this->forge->addUniqueKey(['name', 'academic_year', 'semester']);
        $this->forge->createTable('grading_periods');
    }

    public function down()
    {
        $this->forge->dropTable('grading_periods');
    }
}
