<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Networking 2',
                'course_code' => 'NET202',
                'description' => 'Learn the basics of networking with hands-on exercises and real-world scenarios.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Computer Science',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Web Systems and Technologies',
                'course_code' => 'WEB301',
                'description' => 'Explore HTML, CSS, JavaScript, and backend web development concepts.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Information Technology',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced Database Management Systems',
                'course_code' => 'DB401',
                'description' => 'Understand relational databases, SQL, and data modeling techniques.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Computer Engineering',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Analysis and Design',
                'course_code' => 'SAD201',
                'description' => 'A study of methods for analyzing, designing, and developing effective information systems.',
                'instructor_id' => null,
                'academic_year' => '2023-2024',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Computer Science',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Integration and Architecture 1',
                'course_code' => 'SIA151',
                'description' => 'A study of system integration principles and architectural design.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Computer Science',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'General Mathematics',
                'course_code' => 'GEN101',
                'description' => 'Fundamental mathematical concepts and problem-solving techniques.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Arts',
                'program' => 'General Education',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Business Fundamentals',
                'course_code' => 'BUS101',
                'description' => 'Introduction to business concepts, management principles, and organizational behavior.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Business',
                'program' => 'Bachelor of Science in Business Administration',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Data Structures and Algorithms',
                'course_code' => 'CS201',
                'description' => 'Study of fundamental data structures and algorithmic analysis techniques.',
                'instructor_id' => null,
                'academic_year' => '2024-2025',
                'department' => 'Department of Engineering and Technology',
                'program' => 'Bachelor of Science in Computer Science',
                'term' => null,
                'semester' => null,
                'schedule' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all courses at once
        $this->db->table('courses')->insertBatch($data);
    }
}
