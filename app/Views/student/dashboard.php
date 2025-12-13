<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Student Dashboard</h1>
        <div>
            <span class="text-muted">Welcome, <?= esc($student_name) ?></span>
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

    <!-- Pending Enrollment Requests -->
    <?php if (!empty($pending_enrollments)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Pending Enrollment Requests
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($pending_enrollments as $enrollment): ?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= esc($enrollment['course_code']) ?> - <?= esc($enrollment['course_title']) ?></strong><br>
                                <small class="text-muted">
                                    Teacher: <?= esc($enrollment['teacher_name']) ?> | 
                                    Requested: <?= date('M j, Y', strtotime($enrollment['enrolled_at'])) ?>
                                </small>
                            </div>
                            <div>
                                <a href="<?= base_url('student/enrollment-requests') ?>" class="btn btn-sm btn-warning">
                                    View & Respond
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">My Student Portal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-grid">
                        <a href="<?= base_url('student/my-schedule') ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-calendar3 me-2"></i> My Schedule
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-grid">
                        <a href="<?= base_url('student/my-courses') ?>" class="btn btn-success btn-lg">
                            <i class="bi bi-book me-2"></i> My Courses
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-grid">
                        <a href="<?= base_url('student/materials') ?>" class="btn btn-info btn-lg">
                            <i class="bi bi-file-earmark-text me-2"></i> Materials
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-grid">
                        <a href="<?= base_url('student/enrollment-requests') ?>" class="btn btn-warning btn-lg">
                            <i class="bi bi-envelope-open me-2"></i> Enrollment Requests
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-grid">
                        <a href="<?= base_url('courses') ?>" class="btn btn-secondary btn-lg">
                            <i class="bi bi-search me-2"></i> Browse Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning"><?= count($pending_enrollments) ?></h5>
                    <p class="card-text">Pending Requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-success"><?= $approved_courses ?></h5>
                    <p class="card-text">Approved Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-info"><?= $active_courses ?></h5>
                    <p class="card-text">Active Courses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- No Pending Requests Message -->
    <?php if (empty($pending_enrollments)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                <h5 class="text-success">All Caught Up!</h5>
                <p class="text-muted">You don't have any pending enrollment requests.</p>
                <a href="<?= base_url('courses') ?>" class="btn btn-primary">
                    <i class="bi bi-book me-2"></i> Browse Available Courses
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
