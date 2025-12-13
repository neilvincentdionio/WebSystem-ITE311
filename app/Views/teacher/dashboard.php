<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Teacher Dashboard</h1>
                    <p class="text-muted">Welcome back, <?= esc($teacher_name ?? 'Teacher') ?>!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['total_courses'] ?></h4>
                            <p class="card-text">My Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('teacher/courses') ?>" class="text-white text-decoration-none">View All →</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['total_students'] ?></h4>
                            <p class="card-text">Total Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('teacher/students') ?>" class="text-white text-decoration-none">View Students →</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['total_materials'] ?></h4>
                            <p class="card-text">Materials</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('materials') ?>" class="text-white text-decoration-none">Manage →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Assignments -->
        <div class="col-md-8 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Pending Course Assignments
                        <span class="badge bg-secondary float-end"><?= count($pending_assignments ?? []) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pending_assignments)): ?>
                        <div class="list-group">
                            <?php foreach ($pending_assignments as $assignment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($assignment['course_code']) ?> - <?= esc($assignment['title']) ?></h6>
                                            <p class="mb-1 text-muted">
                                                <small>
                                                    <i class="bi bi-calendar"></i> <?= esc($assignment['schedule'] ?? 'N/A') ?><br>
                                                    <i class="bi bi-building"></i> <?= esc($assignment['academic_year'] ?? 'N/A') ?> - <?= esc($assignment['semester'] ?? 'N/A') ?><br>
                                                    <i class="bi bi-person"></i> Assigned by <?= esc($assignment['assigned_by_name'] ?? 'Admin') ?> on <?= date('M j, Y', strtotime($assignment['assigned_at'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="ms-3">
                                            <div class="btn-group-vertical btn-group-sm">
                                                <form action="<?= base_url('teacher/assignment/accept') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Accept this course assignment?')">
                                                        <i class="bi bi-check-circle"></i> Accept
                                                    </button>
                                                </form>
                                                <form action="<?= base_url('teacher/assignment/reject') ?>" method="post" class="d-inline mt-1">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject this course assignment?')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                            <h6 class="text-success">No Pending Assignments</h6>
                            <p class="text-muted">You have no pending course assignments to review.</p>
                            <?php if (session()->get('role') === 'teacher'): ?>
                                <small class="text-muted">Note: Assignments will appear here when an admin assigns you to a course.</small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-<?php echo !empty($pending_assignments) ? '4' : '8'; ?> mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('materials') ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cloud-upload me-2"></i> Upload Materials
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('teacher/schedule') ?>" class="btn btn-info btn-lg">
                                    <i class="bi bi-calendar me-2"></i> My Schedule
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('teacher/courses') ?>" class="btn btn-success btn-lg">
                                    <i class="bi bi-book me-2"></i> My Courses
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('teacher/students') ?>" class="btn btn-warning btn-lg">
                                    <i class="bi bi-people me-2"></i> My Students
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Enrollments -->
        <div class="col-md-8 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        Student Enrollments
                        <span class="badge bg-light text-dark float-end"><?= count($pending_enrollments ?? []) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pending_enrollments)): ?>
                        <div class="list-group">
                            <?php foreach ($pending_enrollments as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['student_name']) ?></h6>
                                            <p class="mb-1 text-muted">
                                                <small>
                                                    <i class="bi bi-envelope"></i> <?= esc($enrollment['student_email']) ?><br>
                                                    <i class="bi bi-book"></i> <?= esc($enrollment['course_code']) ?> - <?= esc($enrollment['course_title']) ?><br>
                                                    <i class="bi bi-calendar"></i> Requested on <?= date('M j, Y', strtotime($enrollment['enrolled_at'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="ms-3">
                                            <?php
                                            $statusClass = match($enrollment['status']) {
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $statusIcon = match($enrollment['status']) {
                                                'pending' => 'bi-hourglass-split',
                                                'approved' => 'bi-check-circle',
                                                'rejected' => 'bi-x-circle',
                                                default => 'bi-question-circle'
                                            };
                                            $statusText = match($enrollment['status']) {
                                                'pending' => 'Awaiting Student Response',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                default => 'Unknown'
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-person-check fs-1 text-info mb-3"></i>
                            <h6 class="text-info">No Enrollments</h6>
                            <p class="text-muted">You have no student enrollment requests.</p>
                            <small class="text-muted">Note: Enrollment requests will appear here when students request to join your courses.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    </div>

</body>
</html>
