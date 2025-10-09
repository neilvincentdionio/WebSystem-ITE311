<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'        => 'Networking 2',
                'description'  => 'Learn the basics of networking with hands-on exercises and real-world scenarios.',
                'instructor_id'=> 1, // Make sure user ID 1 exists and is a teacher
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Web Systems and Technologies',
                'description'  => 'Explore HTML, CSS, JavaScript, and backend web development concepts.',
                'instructor_id'=> 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Advanced Database Management Systems',
                'description'  => 'Understand relational databases, SQL, and data modeling techniques.',
                'instructor_id'=> 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'System Analysis and Design',
                'description'  => 'A study of methods for analyzing, designing, and developing effective information systems.',
                'instructor_id'=> 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'System Integration and Architecture 1',
                'description'  => 'A study of system integration principles and architectural design.',
                'instructor_id'=> 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all courses at once
        $this->db->table('courses')->insertBatch($data);
    }
}
