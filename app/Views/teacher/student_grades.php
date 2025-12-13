<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Student Grades - <?= esc($course['title']) ?></h1>
        <div>
            <a href="<?= base_url('teacher/grading/' . $course['id']) ?>" class="btn btn-secondary">Back to Grading</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Grade
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkGradeModal">
                    <i class="bi bi-plus-square me-2"></i>Add Multiple Grades
                </button>
                <a href="<?= base_url('teacher/grading/' . $course['id']) ?>" class="btn btn-secondary">
                    <i class="bi bi-calculator me-2"></i>Manage Grading Weights
                </a>
            </div>
        </div>
    </div>

    <!-- Grading Period Selector -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('teacher/student-grades/' . $course['id']) ?>">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="grading_period" class="form-label">Grading Period</label>
                        <select class="form-select" id="grading_period" name="grading_period_id" onchange="this.form.submit()">
                            <option value="">All Periods</option>
                            <?php foreach ($grading_periods as $period): ?>
                                <option value="<?= $period['id'] ?>" 
                                    <?= ($selected_period && $selected_period == $period['id']) ? 'selected' : '' ?>>
                                    <?= esc($period['name']) ?> - <?= esc($period['academic_year']) ?>
                                    (<?= ucfirst($period['semester']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Student Grades</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($students)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Assignment Type</th>
                                <th>Score</th>
                                <th>Max Score</th>
                                <th>Percentage</th>
                                <th>Weight</th>
                                <th>Weighted Score</th>
                                <th>Graded At</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $studentGrades = [];
                            foreach ($students as $student): 
                                // Group grades by student for summary
                                if (!isset($studentGrades[$student['student_id']])) {
                                    $studentGrades[$student['student_id']] = [
                                        'name' => $student['student_name'],
                                        'weighted_total' => 0,
                                        'grades' => []
                                    ];
                                }
                                $studentGrades[$student['student_id']]['grades'][] = $student;
                                $studentGrades[$student['student_id']]['weighted_total'] += $student['weighted_score'];
                            ?>
                            <tr>
                                <td><?= esc($student['student_name']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= esc($student['assignment_type_name']) ?></span>
                                </td>
                                <td><?= number_format($student['score'], 2) ?></td>
                                <td><?= number_format($student['max_score'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $student['percentage_score'] >= 75 ? 'bg-success' : ($student['percentage_score'] >= 60 ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= number_format($student['percentage_score'], 1) ?>%
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $weight = 0;
                                    foreach ($weights as $w) {
                                        if ($w['assignment_type_id'] == $student['assignment_type_id']) {
                                            $weight = $w['weight_percentage'];
                                            break;
                                        }
                                    }
                                    echo number_format($weight, 1) . '%';
                                    ?>
                                </td>
                                <td>
                                    <strong><?= number_format($student['weighted_score'], 2) ?></strong>
                                </td>
                                <td><?= date('M j, Y H:i', strtotime($student['graded_at'])) ?></td>
                                <td><?= esc($student['remarks'] ?? '-') ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGradeModal"
                                            data-student-id="<?= $student['student_id'] ?>"
                                            data-assignment-type-id="<?= $student['assignment_type_id'] ?>"
                                            data-score="<?= $student['score'] ?>"
                                            data-max-score="<?= $student['max_score'] ?>"
                                            data-remarks="<?= esc($student['remarks'] ?? '') ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Grade Summary -->
                <div class="mt-4">
                    <h6>Grade Summary</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Total Weighted Score</th>
                                    <th>Final Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentGrades as $studentId => $studentData): ?>
                                <tr>
                                    <td><?= esc($studentData['name']) ?></td>
                                    <td><?= number_format($studentData['weighted_total'], 2) ?></td>
                                    <td>
                                        <strong><?= number_format($studentData['weighted_total'], 1) ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        $finalGrade = $studentData['weighted_total'];
                                        if ($finalGrade >= 75) {
                                            echo '<span class="badge bg-success">Passed</span>';
                                        } elseif ($finalGrade >= 60) {
                                            echo '<span class="badge bg-warning">Conditional</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">Failed</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Grades Found</h5>
                    <p class="text-muted">No grades have been recorded for this course yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                        <i class="bi bi-plus-circle me-2"></i>Add First Grade
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Grade Modal -->
    <div class="modal fade" id="addGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="<?= base_url('teacher/save-grade') ?>">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        <input type="hidden" name="grading_period_id" value="<?= $selected_period ?? '' ?>">
                        
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" id="student_id" name="student_id" required>
                                <option value="">Select student...</option>
                                <?php foreach ($enrolled_students as $student): ?>
                                    <option value="<?= $student['student_id'] ?>"><?= esc($student['student_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="assignment_type_id" class="form-label">Assignment Type</label>
                            <select class="form-select" id="assignment_type_id" name="assignment_type_id" required>
                                <option value="">Select assignment type...</option>
                                <?php foreach ($assignment_types as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="score" class="form-label">Score</label>
                                    <input type="number" class="form-control" id="score" name="score" 
                                           min="0" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_score" class="form-label">Max Score</label>
                                    <input type="number" class="form-control" id="max_score" name="max_score" 
                                           min="0" step="0.01" value="100" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Grade Modal -->
    <div class="modal fade" id="editGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="<?= base_url('teacher/save-grade') ?>">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        <input type="hidden" name="grading_period_id" value="<?= $selected_period ?? '' ?>">
                        <input type="hidden" id="edit_student_id" name="student_id">
                        <input type="hidden" id="edit_assignment_type_id" name="assignment_type_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_score" class="form-label">Score</label>
                                    <input type="number" class="form-control" id="edit_score" name="score" 
                                           min="0" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_max_score" class="form-label">Max Score</label>
                                    <input type="number" class="form-control" id="edit_max_score" name="max_score" 
                                           min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Bulk Grade Modal -->
    <div class="modal fade" id="bulkGradeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Multiple Grades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="<?= base_url('teacher/save-bulk-grades') ?>">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        <input type="hidden" name="grading_period_id" value="<?= $selected_period ?? '' ?>">
                        
                        <div class="mb-3">
                            <label for="bulk_assignment_type_id" class="form-label">Assignment Type</label>
                            <select class="form-select" id="bulk_assignment_type_id" name="assignment_type_id" required>
                                <option value="">Select assignment type...</option>
                                <?php foreach ($assignment_types as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulk_max_score" class="form-label">Max Score</label>
                                    <input type="number" class="form-control" id="bulk_max_score" name="max_score" 
                                           min="0" step="0.01" value="100" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulk_remarks" class="form-label">Remarks (Optional)</label>
                                    <input type="text" class="form-control" id="bulk_remarks" name="remarks" 
                                           placeholder="e.g., Quiz #1, Midterm Exam">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Student Grades</label>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Score</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrolled_students as $student): ?>
                                        <tr>
                                            <td><?= esc($student['student_name']) ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm bulk-score-input" 
                                                       name="grades[<?= $student['student_id'] ?>]" 
                                                       min="0" step="0.01" 
                                                       placeholder="0.00"
                                                       data-max-score="100">
                                            </td>
                                            <td>
                                                <span class="percentage-display">0%</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save All Grades</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit grade modal
    const editGradeModal = document.getElementById('editGradeModal');
    editGradeModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        
        document.getElementById('edit_student_id').value = button.dataset.studentId;
        document.getElementById('edit_assignment_type_id').value = button.dataset.assignmentTypeId;
        document.getElementById('edit_score').value = button.dataset.score;
        document.getElementById('edit_max_score').value = button.dataset.maxScore;
        document.getElementById('edit_remarks').value = button.dataset.remarks;
    });

    // Handle bulk grade modal percentage calculation
    const bulkScoreInputs = document.querySelectorAll('.bulk-score-input');
    const bulkMaxScoreInput = document.getElementById('bulk_max_score');
    
    function updatePercentages() {
        const maxScore = parseFloat(bulkMaxScoreInput.value) || 100;
        
        bulkScoreInputs.forEach(input => {
            const score = parseFloat(input.value) || 0;
            const percentage = (score / maxScore) * 100;
            const percentageDisplay = input.closest('tr').querySelector('.percentage-display');
            percentageDisplay.textContent = percentage.toFixed(1) + '%';
            
            // Update data-max-score attribute
            input.dataset.maxScore = maxScore;
            
            // Color code the percentage
            if (percentage >= 75) {
                percentageDisplay.className = 'badge bg-success';
            } else if (percentage >= 60) {
                percentageDisplay.className = 'badge bg-warning';
            } else {
                percentageDisplay.className = 'badge bg-danger';
            }
        });
    }
    
    bulkMaxScoreInput.addEventListener('input', updatePercentages);
    bulkScoreInputs.forEach(input => {
        input.addEventListener('input', updatePercentages);
    });
    
    // Initial calculation
    updatePercentages();
});
</script>

