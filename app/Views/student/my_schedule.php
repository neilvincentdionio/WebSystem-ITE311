<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Schedule</h1>
        <div>
            <span class="text-muted">Welcome, <?= esc($student_name) ?></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('student/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">My Schedule</li>
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
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar3 me-2"></i>
                My Class Schedule
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Teacher</th>
                                <th>Schedule</th>
                                <th>Room</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><strong><?= esc($course['course_code']) ?></strong></td>
                                    <td><?= esc($course['title']) ?></td>
                                    <td>
                                        <div>
                                            <strong><?= esc($course['teacher_name'] ?? 'Not Assigned') ?></strong>
                                            <?php if (!empty($course['teacher_email'])): ?>
                                                <br><small class="text-muted"><?= esc($course['teacher_email']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= esc($course['schedule']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['room_number']) && !empty($course['building'])): ?>
                                            <span class="badge bg-secondary">
                                                <?= esc($course['room_number']) ?> (<?= esc($course['building']) ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('student/my-courses') ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-book"></i> View Course
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Schedule Summary -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Schedule Summary</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Courses:</strong> <?= count($courses) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Scheduled Courses:</strong> 
                                        <?= count(array_filter($courses, fn($c) => $c['schedule'] !== 'Schedule not set')) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Assigned Teachers:</strong> 
                                        <?= count(array_filter($courses, fn($c) => !empty($c['teacher_name']))) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar3 fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Schedule Available</h5>
                    <p class="text-muted">You don't have any approved courses with scheduled times yet.</p>
                    <a href="<?= base_url('student/my-courses') ?>" class="btn btn-primary">
                        <i class="bi bi-book me-2"></i> View My Courses
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
