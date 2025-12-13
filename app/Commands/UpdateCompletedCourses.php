<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CourseModel;

class UpdateCompletedCourses extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'courses:update-completed';
    protected $description = 'Auto-update course status to Completed for ended courses with enrolled students';
    protected $usage = 'courses:update-completed';
    protected $arguments = [];
    protected $options = [];

    public function run(array $params)
    {
        $courseModel = new CourseModel();
        
        CLI::write('Starting course status update process...', 'green');
        
        $updatedCount = $courseModel->updateAllCompletedCourses();
        
        if ($updatedCount > 0) {
            CLI::write("Successfully updated {$updatedCount} course(s) to 'Completed' status.", 'green');
        } else {
            CLI::write('No courses required status updates.', 'yellow');
        }
        
        CLI::write('Course status update process completed.', 'green');
    }
}
