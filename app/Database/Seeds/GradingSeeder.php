<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GradingSeeder extends Seeder
{
    public function run()
    {
        $this->db = \Config\Database::connect();
        
        // Insert Grading Periods
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        $gradingPeriods = [
            [
                'name' => 'Prelim',
                'description' => 'Preliminary Examination Period',
                'academic_year' => $currentYear . '-' . $nextYear,
                'semester' => 'first',
                'start_date' => $currentYear . '-08-01',
                'end_date' => $currentYear . '-10-15',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Midterm',
                'description' => 'Midterm Examination Period',
                'academic_year' => $currentYear . '-' . $nextYear,
                'semester' => 'first',
                'start_date' => $currentYear . '-10-16',
                'end_date' => $currentYear . '-12-20',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Finals',
                'description' => 'Final Examination Period',
                'academic_year' => $currentYear . '-' . $nextYear,
                'semester' => 'first',
                'start_date' => $currentYear . '-12-21',
                'end_date' => $nextYear . '-03-15',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($gradingPeriods as $period) {
            $this->db->table('grading_periods')->insert($period);
        }

        echo "Grading system data seeded successfully!\n";
    }
}
