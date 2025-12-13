<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Grading System - <?= esc($course['title']) ?></h1>
        <a href="<?= base_url('teacher/courses') ?>" class="btn btn-secondary">Back to Courses</a>
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

    <!-- Grading Periods and Weights Setup -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Set Grading Weights</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('teacher/set-grading-weights') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="grading_period_id" class="form-label">Grading Period</label>
                            <select class="form-select" id="grading_period_id" name="grading_period_id" required>
                                <option value="">Select grading period...</option>
                                <?php foreach ($grading_periods as $period): ?>
                                    <option value="<?= $period['id'] ?>" 
                                        <?= ($active_period && $active_period['id'] == $period['id']) ? 'selected' : '' ?>>
                                        <?= esc($period['name']) ?> - <?= esc($period['academic_year']) ?> 
                                        (<?= ucfirst($period['semester']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assignment Weights (must total 100%)</label>
                            <?php foreach ($assignment_types as $type): ?>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small"><?= esc($type['name']) ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="number" class="form-control weight-input" 
                                                name="weights[<?= $type['id'] ?>]" 
                                                min="0" max="100" step="0.01" 
                                                placeholder="0.00" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total Weight:</span>
                                <span id="total-weight" class="badge bg-primary">0%</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success" id="save-weights-btn" disabled>
                            <i class="bi bi-save me-1"></i> Save Weights
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('teacher/student-grades/' . $course['id']) ?>" class="btn btn-primary">
                            <i class="bi bi-clipboard-data me-2"></i> View Student Grades
                        </a>
                        <a href="<?= base_url('teacher/students') ?>" class="btn btn-info">
                            <i class="bi bi-people me-2"></i> My Students
                        </a>
                        <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" class="btn btn-secondary">
                            <i class="bi bi-folder me-2"></i> Course Materials
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assignment Types</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($assignment_types as $type): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>
                                <i class="bi bi-tag me-2"></i><?= esc($type['name']) ?>
                            </span>
                            <span class="badge bg-secondary"><?= esc($type['code']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const weightInputs = document.querySelectorAll('.weight-input');
    const totalWeightElement = document.getElementById('total-weight');
    const saveWeightsBtn = document.getElementById('save-weights-btn');

    function calculateTotal() {
        let total = 0;
        weightInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });

        totalWeightElement.textContent = total.toFixed(2) + '%';
        
        // Enable/disable save button based on total
        if (total === 100) {
            totalWeightElement.className = 'badge bg-success';
            saveWeightsBtn.disabled = false;
        } else {
            totalWeightElement.className = 'badge bg-warning';
            saveWeightsBtn.disabled = true;
        }
    }

    weightInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation
    calculateTotal();
});
</script>

<?= $this->include('templates/footer') ?>
