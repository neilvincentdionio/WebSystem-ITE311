 <?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>System Overview</h2>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Courses Card -->
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">Courses</h5>
                    <h3 class="text-primary"><?= $stats['courses']['total'] ?></h3>
                    <p class="card-text small text-muted">
                        <?= $stats['courses']['with_teachers'] ?> with teachers<br>
                        <?= $stats['courses']['without_teachers'] ?> need teachers
                    </p>
                    <a href="<?= base_url('admin/courses') ?>" class="btn btn-sm btn-outline-primary">Manage Courses</a>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h5 class="card-title">Users</h5>
                    <h3 class="text-success"><?= $stats['users']['total'] ?></h3>
                    <p class="card-text small text-muted">
                        <?= $stats['users']['teachers'] ?> teachers<br>
                        <?= $stats['users']['students'] ?> students<br>
                        <?= $stats['users']['admins'] ?> admins
                    </p>
                    <a href="<?= base_url('users') ?>" class="btn btn-sm btn-outline-success">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- Teacher Assignments Card -->
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">Assignments</h5>
                    <h3 class="text-warning"><?= $stats['assignments']['total'] ?></h3>
                    <p class="card-text small text-muted">
                        <?= $stats['assignments']['pending'] ?> pending<br>
                        <?= $stats['assignments']['approved'] ?> approved<br>
                        <?= $stats['assignments']['rejected'] ?> rejected
                    </p>
                    <a href="<?= base_url('admin/assignments') ?>" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
            </div>
        </div>

        <!-- Student Enrollments Card -->
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-graduation-cap fa-2x text-info mb-2"></i>
                    <h5 class="card-title">Enrollments</h5>
                    <h3 class="text-info"><?= $stats['enrollments']['total'] ?></h3>
                    <p class="card-text small text-muted">
                        <?= $stats['enrollments']['pending'] ?> pending<br>
                        <?= $stats['enrollments']['approved'] ?> approved<br>
                        <?= $stats['enrollments']['rejected'] ?> rejected
                    </p>
                    <a href="<?= base_url('admin/student-enrollments/pending') ?>" class="btn btn-sm btn-outline-info">View All</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Pending Teacher Assignments -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Pending Teacher Assignments
                    </h6>
                    <span class="badge bg-warning"><?= count($recent_activities['pending_assignments'] ?? []) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activities['pending_assignments'])): ?>
                        <p class="text-muted text-center mb-0">No pending assignments</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activities['pending_assignments'] as $assignment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($assignment['course_code'] ?? $assignment['course_title']) ?> - <?= esc($assignment['title'] ?? '') ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> Teacher: <?= esc($assignment['teacher_name']) ?>
                                            </small>
                                        </div>
                                        <div class="ms-2">
                                            <?php if ($assignment['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($assignment['status'] === 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($assignment['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Unknown</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                                            <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pending Student Enrollments -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-clock text-info me-2"></i>
                        Pending Student Enrollments
                    </h6>
                    <span class="badge bg-info"><?= count($recent_activities['pending_enrollments']) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activities['pending_enrollments'])): ?>
                        <p class="text-muted text-center mb-0">No pending enrollments</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activities['pending_enrollments'] as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> Student: <?= esc($enrollment['student_name']) ?>
                                            </small>
                                        </div>
                                        <div class="ms-2">
                                            <?php if ($enrollment['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($enrollment['status'] === 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($enrollment['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Unknown</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted"><?= date('M j, Y H:i', strtotime($enrollment['enrolled_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($recent_activities['pending_enrollments']) >= 5): ?>
                            <div class="text-center mt-2">
                                <a href="<?= base_url('approval/student/enrollments') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="<?= base_url('admin/courses/create') ?>" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Add New Course
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Manage Enrollments
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="<?= base_url('admin/schedules') ?>" class="btn btn-warning w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Manage Schedules
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
