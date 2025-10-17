<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome Back Students!',
                'content' => 'The new semester starts on January 5, 2025. Please check your schedule.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'System Maintenance Notice',
                'content' => 'The portal will undergo maintenance on October 25, 2025, from 8 PM to 10 PM.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
