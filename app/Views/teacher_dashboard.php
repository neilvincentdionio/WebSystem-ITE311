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
                <div>
                    <span class="badge bg-primary fs-6"><?= date('l, F j, Y') ?></span>
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
                    <a href="<?= base_url('my_courses') ?>" class="text-white text-decoration-none">View All →</a>
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
                    <a href="<?= base_url('my_students') ?>" class="text-white text-decoration-none">View Students →</a>
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

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Active</h4>
                            <p class="card-text">Schedule</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('teacher/schedule') ?>" class="text-dark text-decoration-none">View Schedule →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-md-8 mb-4">
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
                                    <i class="bi bi-calendar me-2"></i> View Schedule
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('my_courses') ?>" class="btn btn-success btn-lg">
                                    <i class="bi bi-book me-2"></i> My Courses
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="<?= base_url('my_students') ?>" class="btn btn-warning btn-lg">
                                    <i class="bi bi-people me-2"></i> My Students
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recent_activities'])): ?>
                        <?php foreach ($stats['recent_activities'] as $activity): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <?php if ($activity['type'] === 'upload'): ?>
                                        <i class="bi bi-cloud-upload text-primary fs-5"></i>
                                    <?php elseif ($activity['type'] === 'enrollment'): ?>
                                        <i class="bi bi-person-plus text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-book text-info fs-5"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold"><?= esc($activity['description']) ?></div>
                                    <small class="text-muted"><?= esc($activity['time']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent activities</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- My Courses Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">My Courses</h5>
                    <a href="<?= base_url('my_courses') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($courses)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Schedule</th>
                                        <th>Students</th>
                                        <th>Materials</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($courses, 0, 5) as $course): ?>
                                        <tr>
                                            <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['title'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?= $this->enrollments->getEnrollmentCount($course['id']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= $this->materials->getMaterialsCount($course['id']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('course/' . $course['id'] . '/upload') ?>" 
                                                       class="btn btn-outline-primary" title="Upload Materials">
                                                        <i class="bi bi-upload"></i>
                                                    </a>
                                                    <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" 
                                                       class="btn btn-outline-info" title="View Materials">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($courses) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('my_courses') ?>" class="btn btn-primary">View All Courses</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-book fs-1 text-muted mb-3"></i>
                            <p class="text-muted">You haven't been assigned to any courses yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">System Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-database text-primary me-2"></i>
                            <strong>Database:</strong> Connected
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-server text-success me-2"></i>
                            <strong>Server:</strong> Online
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock text-info me-2"></i>
                            <strong>Last Backup:</strong> Today at 2:00 AM
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield-check text-warning me-2"></i>
                            <strong>Security:</strong> All systems operational
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="<?= base_url('announcements') ?>" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-megaphone me-1"></i> Announcements
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-gear me-1"></i> Admin Panel
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="#" class="btn btn-outline-info btn-sm w-100">
                                <i class="bi bi-question-circle me-1"></i> Help
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
