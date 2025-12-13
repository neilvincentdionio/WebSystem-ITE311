<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Courses</h1>
        <div>
            <span class="text-muted">Welcome, <?= esc($student_name) ?></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('student/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">My Courses</li>
        </ol>
    </nav>

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

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-book me-2"></i>
                My Enrolled Courses
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <strong><?= esc($course['course_code']) ?></strong>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                    
                                    <?php if (!empty($course['description'])): ?>
                                        <p class="card-text text-muted"><?= esc(substr($course['description'], 0, 150)) ?>...</p>
                                    <?php endif; ?>
                                    
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <strong>Teacher:</strong> <?= esc($course['teacher_name'] ?? 'Not Assigned') ?><br>
                                                <?php if (!empty($course['teacher_email'])): ?>
                                                    <strong>Email:</strong> <?= esc($course['teacher_email']) ?><br>
                                                <?php endif; ?>
                                                <strong>Department:</strong> <?= esc($course['department'] ?? 'N/A') ?><br>
                                                <strong>Program:</strong> <?= esc($course['program'] ?? 'N/A') ?><br>
                                                <strong>Semester:</strong> <?= esc($course['semester'] ?? 'N/A') ?><br>
                                                <strong>Term:</strong> <?= esc($course['term'] ?? 'N/A') ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-success">Approved</span>
                                            <span class="badge bg-info"><?= $course['materials_count'] ?> Materials</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('student/materials') ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-file-earmark-text"></i> Materials
                                            </a>
                                            <a href="<?= base_url('student/my-schedule') ?>" class="btn btn-outline-secondary">
                                                <i class="bi bi-calendar3"></i> Schedule
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Course Summary -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Course Summary</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Total Courses:</strong> <?= count($courses) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Materials:</strong> 
                                        <?= array_sum(array_column($courses, 'materials_count')) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Assigned Teachers:</strong> 
                                        <?= count(array_filter($courses, fn($c) => !empty($c['teacher_name']))) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Departments:</strong> 
                                        <?= count(array_unique(array_column($courses, 'department'))) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Courses Enrolled</h5>
                    <p class="text-muted">You don't have any approved course enrollments yet.</p>
                    <div class="mt-3">
                        <a href="<?= base_url('courses') ?>" class="btn btn-primary me-2">
                            <i class="bi bi-search me-2"></i> Browse Courses
                        </a>
                        <a href="<?= base_url('student/enrollment-requests') ?>" class="btn btn-warning">
                            <i class="bi bi-envelope-open me-2"></i> Check Enrollment Requests
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
