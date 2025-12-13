<?= $this->include('templates/header') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Assign Teacher to Course</h2>
        <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Enrollments
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> Please fix the following issues:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Course Information</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5><?= esc($course['title']) ?></h5>
                    <p class="text-muted">
                        <strong>Course Code:</strong> <?= esc($course['course_code']) ?> | 
                        <strong>Department:</strong> <?= esc($course['department']) ?> | 
                        <strong>Program:</strong> <?= esc($course['program']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-dark text-white">Assign Teacher</div>
        <div class="card-body">
            <form action="<?= base_url('admin/courses/assign-teacher/' . $course['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Select Teacher *</label>
                    <select class="form-select" id="teacher_id" name="teacher_id" required>
                        <option value="">Select Teacher</option>
                        <?php if (isset($teachers) && is_array($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= esc($teacher['id']) ?>" 
                                        <?= old('teacher_id', $course['teacher_id']) == $teacher['id'] ? 'selected' : '' ?>>
                                    <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">Select a teacher to assign to this course. After selection, you'll be directed to Manage Schedules to configure the schedule details.</div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Workflow:</strong> After selecting a teacher, you will be redirected to the Manage Schedules page where you can configure the schedule details (days, times, room, etc.) before the assignment is sent to the teacher for approval.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Manage Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($current_teacher)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">Current Teacher Assignment</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?= esc($current_teacher['name']) ?></p>
                        <p><strong>Email:</strong> <?= esc($current_teacher['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Role:</strong> <?= esc($current_teacher['role']) ?></p>
                        <p><strong>Assigned:</strong> <?= date('M d, Y h:i A', strtotime($course['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
