<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Teacher User',
                'email'    => 'teacher@example.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
            ],
            [
                'name'     => 'Student User',
                'email'    => 'student@example.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
            ],
        ];

        // Idempotent insert: skip duplicates for unique emails
        $builder = $this->db->table('users');
        $builder->ignore(true)->insertBatch($data);
    }
}
