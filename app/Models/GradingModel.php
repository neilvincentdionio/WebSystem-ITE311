<?php

namespace App\Models;

use CodeIgniter\Model;

class GradingModel extends Model
{
    protected $table = 'student_grades';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'student_id', 'course_id', 'grading_period_id', 'assignment_type_id',
        'score', 'max_score', 'percentage_score', 'weighted_score', 'remarks',
        'graded_by', 'graded_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Grading Periods
    public function getGradingPeriods()
    {
        return $this->db->table('grading_periods')
            ->orderBy('academic_year', 'DESC')
            ->orderBy('semester', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getActiveGradingPeriod()
    {
        return $this->db->table('grading_periods')
            ->where('is_active', 1)
            ->get()
            ->getRowArray();
    }

    // Assignment Types
    public function getAssignmentTypes()
    {
        return $this->db->table('assignment_types')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Grading Weights
    public function getGradingWeights($courseId, $gradingPeriodId = null)
    {
        $builder = $this->db->table('grading_weights gw')
            ->select('gw.*, at.name as assignment_type_name, at.code as assignment_type_code')
            ->join('assignment_types at', 'at.id = gw.assignment_type_id')
            ->where('gw.course_id', $courseId);

        if ($gradingPeriodId) {
            $builder->where('gw.grading_period_id', $gradingPeriodId);
        }

        return $builder->orderBy('at.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function setGradingWeights($courseId, $gradingPeriodId, $weights)
    {
        $this->db->transStart();

        try {
            // Remove existing weights for this course and period
            $this->db->table('grading_weights')
                ->where('course_id', $courseId)
                ->where('grading_period_id', $gradingPeriodId)
                ->delete();

            // Insert new weights
            foreach ($weights as $assignmentTypeId => $weightPercentage) {
                $this->db->table('grading_weights')->insert([
                    'course_id' => $courseId,
                    'grading_period_id' => $gradingPeriodId,
                    'assignment_type_id' => $assignmentTypeId,
                    'weight_percentage' => $weightPercentage,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Grading weights error: {err}', ['err' => $e->getMessage()]);
            return false;
        }
    }

    // Student Grades
    public function getStudentGrades($courseId, $gradingPeriodId = null, $studentId = null)
    {
        $builder = $this->db->table('student_grades sg')
            ->select('sg.*, u.name as student_name, at.name as assignment_type_name, gp.name as grading_period_name')
            ->join('users u', 'u.id = sg.student_id')
            ->join('assignment_types at', 'at.id = sg.assignment_type_id')
            ->join('grading_periods gp', 'gp.id = sg.grading_period_id')
            ->where('sg.course_id', $courseId);

        if ($gradingPeriodId) {
            $builder->where('sg.grading_period_id', $gradingPeriodId);
        }

        if ($studentId) {
            $builder->where('sg.student_id', $studentId);
        }

        return $builder->orderBy('u.name', 'ASC')
            ->orderBy('at.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function saveStudentGrade($data)
    {
        // Calculate percentage and weighted scores
        $maxScore = $data['max_score'];
        $score = $data['score'];
        
        // Get weight for this assignment type
        $weight = $this->db->table('grading_weights')
            ->where('course_id', $data['course_id'])
            ->where('grading_period_id', $data['grading_period_id'])
            ->where('assignment_type_id', $data['assignment_type_id'])
            ->get()
            ->getRowArray();

        $percentageScore = ($score / $maxScore) * 100;
        $weightedScore = $percentageScore * ($weight['weight_percentage'] / 100);

        $gradeData = [
            'student_id' => $data['student_id'],
            'course_id' => $data['course_id'],
            'grading_period_id' => $data['grading_period_id'],
            'assignment_type_id' => $data['assignment_type_id'],
            'score' => $score,
            'max_score' => $maxScore,
            'percentage_score' => round($percentageScore, 2),
            'weighted_score' => round($weightedScore, 2),
            'remarks' => $data['remarks'] ?? null,
            'graded_by' => $data['graded_by'],
            'graded_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Check if grade already exists
        $existing = $this->db->table('student_grades')
            ->where('student_id', $data['student_id'])
            ->where('course_id', $data['course_id'])
            ->where('grading_period_id', $data['grading_period_id'])
            ->where('assignment_type_id', $data['assignment_type_id'])
            ->get()
            ->getRowArray();

        if ($existing) {
            return $this->db->table('student_grades')
                ->where('id', $existing['id'])
                ->update($gradeData);
        } else {
            return $this->db->table('student_grades')->insert($gradeData);
        }
    }

    public function computeFinalGrade($studentId, $courseId, $gradingPeriodId)
    {
        $grades = $this->db->table('student_grades')
            ->select('weighted_score')
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('grading_period_id', $gradingPeriodId)
            ->get()
            ->getResultArray();

        $totalWeightedScore = 0;
        foreach ($grades as $grade) {
            $totalWeightedScore += $grade['weighted_score'];
        }

        return round($totalWeightedScore, 2);
    }

    public function getStudentGradeSummary($studentId, $courseId, $gradingPeriodId = null)
    {
        $builder = $this->db->table('student_grades sg')
            ->select('
                sg.student_id,
                sg.course_id,
                sg.grading_period_id,
                gp.name as grading_period_name,
                SUM(sg.weighted_score) as total_weighted_score,
                COUNT(*) as total_assignments,
                AVG(sg.percentage_score) as average_percentage
            ')
            ->join('grading_periods gp', 'gp.id = sg.grading_period_id')
            ->where('sg.student_id', $studentId)
            ->where('sg.course_id', $courseId);

        if ($gradingPeriodId) {
            $builder->where('sg.grading_period_id', $gradingPeriodId);
        }

        return $builder->groupBy('sg.grading_period_id')
            ->get()
            ->getResultArray();
    }
}
