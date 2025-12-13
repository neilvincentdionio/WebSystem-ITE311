<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Schedule</h1>
        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
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

    <div class="card">
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Schedule</th>
                                <th>Room</th>
                                <th>Building</th>
                                <th>Approved</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($course['course_code']) ?></strong>
                                    </td>
                                    <td><?= esc($course['title']) ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= esc($course['schedule'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($course['room_number'] ?? 'TBA') ?></td>
                                    <td><?= esc($course['building'] ?? 'TBA') ?></td>
                                    <td>
                                        <?php if ($course['responded_at']): ?>
                                            <small class="text-success">
                                                <?= date('M j, Y', strtotime($course['responded_at'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
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
                
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Schedule Summary</h6>
                                    <p class="card-text">
                                        <strong>Total Courses:</strong> <?= count($courses) ?><br>
                                        <strong>Active Schedule:</strong> <?= count(array_filter($courses, fn($c) => $c['schedule'] !== 'N/A')) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-book me-1"></i> View All Courses
                                        </a>
                                        <a href="<?= base_url('materials') ?>" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-cloud-upload me-1"></i> Upload Materials
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Schedule Found</h5>
                    <p class="text-muted">You don't have any scheduled courses at this time.</p>
                    <a href="<?= base_url('teacher/courses') ?>" class="btn btn-primary">
                        <i class="bi bi-book me-2"></i> View My Courses
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
