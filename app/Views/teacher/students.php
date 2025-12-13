<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Students</h1>
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
        <div class="card-header">
            <h5 class="card-title mb-0">Students Enrolled in My Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($students)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= esc($student['student_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['student_email'] ?? 'N/A') ?></td>
                                    <td>
                                        <div>
                                            <span class="badge bg-primary">
                                                <?= esc($student['course_code'] ?? 'N/A') ?>
                                            </span>
                                            <br>
                                            <small class="text-muted"><?= esc($student['course_title'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td><?= esc(date('M j, Y', strtotime($student['enrolled_at'] ?? 'now'))) ?></td>
                                    <td>
                                        <span class="badge bg-success">Approved</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('teacher/grading/' . $student['course_id']) ?>" 
                                               class="btn btn-outline-success" title="Manage Grades">
                                                Grades
                                            </a>
                                            <a href="<?= base_url('teacher/student-grades/' . $student['course_id']) ?>" 
                                               class="btn btn-outline-info" title="View Grades">
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Students Found</h5>
                    <p class="text-muted">No students are currently enrolled in your courses.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
