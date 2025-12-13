<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
      public function up()
    {
        // Drop the existing courses table
        $this->forge->dropTable('courses', true);
        
        // Create the new courses table with simplified structure
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'course_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'instructor_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true, // Make nullable since you'll handle instructor assignments separately
            ],
            'academic_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('instructor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}