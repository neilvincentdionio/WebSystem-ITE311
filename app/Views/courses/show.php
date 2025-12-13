<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('courses') ?>">Courses</a></li>
                    <li class="breadcrumb-item active"><?= esc($course['title']) ?></li>
                </ol>
            </nav>

            <!-- Course Details Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><?= esc($course['title']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Course Information -->
                        <div class="col-md-8">
                            <h5 class="mb-3">Course Information</h5>
                            <p><strong>Description:</strong></p>
                            <p><?= esc($course['description'] ?? 'No description available') ?></p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Course Code:</strong> <?= esc($course['course_code'] ?? 'N/A') ?></p>
                                    <p><strong>Schedule:</strong> <?= esc($course['schedule'] ?? 'N/A') ?></p>
                                    <p><strong>Time:</strong> <?= esc($course['time'] ?? 'N/A') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Academic Year:</strong> <?= esc($course['academic_year'] ?? 'N/A') ?></p>
                                    <p><strong>Semester:</strong> <?= esc($course['semester'] ?? 'N/A') ?></p>
                                    <p><strong>Term:</strong> <?= esc($course['term'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Course Stats -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Course Statistics</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="bi bi-file-earmark-text me-1"></i> Materials</span>
                                        <strong><?= $materialsCount ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="bi bi-people me-1"></i> Enrolled Students</span>
                                        <strong><?= $enrolledCount ?></strong>
                                    </div>
                                    <?php if ($teacher): ?>
                                        <div class="mt-3 pt-3 border-top">
                                            <p class="mb-1"><strong>Instructor:</strong></p>
                                            <p class="mb-0"><?= esc($teacher['name']) ?></p>
                                            <small class="text-muted"><?= esc($teacher['email']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Actions -->
                    <?php if (session()->get('isLoggedIn') && strtolower(session()->get('role')) === 'student'): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <?php if ($isEnrolled): ?>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Enrollment Status: 
                                                        <span class="badge bg-<?= $enrollmentStatus === 'approved' ? 'success' : ($enrollmentStatus === 'pending' ? 'warning' : 'danger') ?>">
                                                            <?= ucfirst($enrollmentStatus) ?>
                                                        </span>
                                                    </h6>
                                                    <p class="mb-0 text-muted">
                                                        <?php if ($enrollmentStatus === 'approved'): ?>
                                                            You are enrolled in this course. You can access materials and view your schedule.
                                                        <?php elseif ($enrollmentStatus === 'pending'): ?>
                                                            Your enrollment request is pending approval from the administrator.
                                                        <?php else: ?>
                                                            Your enrollment request was rejected. Please contact the administrator for more information.
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <?php if ($enrollmentStatus === 'approved'): ?>
                                                    <div>
                                                        <a href="<?= base_url('student/my-courses') ?>" class="btn btn-primary me-2">
                                                            <i class="bi bi-book me-1"></i> My Courses
                                                        </a>
                                                        <a href="<?= base_url('student/materials') ?>" class="btn btn-info">
                                                            <i class="bi bi-file-earmark-text me-1"></i> Materials
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center">
                                                <h6 class="mb-3">Not Enrolled</h6>
                                                <p class="text-muted mb-3">You are not enrolled in this course. Contact your administrator for enrollment information.</p>
                                                <a href="<?= base_url('courses') ?>" class="btn btn-outline-primary">
                                                    <i class="bi bi-arrow-left me-1"></i> Back to Courses
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('courses') ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Courses
                                </a>
                                <?php if (session()->get('isLoggedIn') && strtolower(session()->get('role')) === 'admin'): ?>
                                    <div>
                                        <a href="<?= base_url('admin/enroll-student/' . $course['id']) ?>" class="btn btn-success me-2">
                                            <i class="bi bi-person-plus me-1"></i> Enroll Student
                                        </a>
                                        <a href="<?= base_url('course/' . $course['id'] . '/upload') ?>" class="btn btn-primary">
                                            <i class="bi bi-upload me-1"></i> Upload Materials
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
