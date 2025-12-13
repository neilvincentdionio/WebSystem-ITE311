<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Materials Management</h1>
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
            <h5 class="card-title mb-0">Select a Course to Manage Materials</h5>
        </div>
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-folder-open fs-1 text-muted mb-3"></i>
                    <p class="text-muted">
                        <?php if ($role === 'teacher'): ?>
                            You haven't been assigned to any courses yet.
                        <?php else: ?>
                            No courses found. Please create courses first.
                        <?php endif; ?>
                    </p>
                    <?php if ($role === 'admin'): ?>
                        <a href="<?= base_url('admin/courses') ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Create Course
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Schedule</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['title'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['academic_year'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('course/' . $course['id'] . '/upload') ?>" 
                                               class="btn btn-primary btn-sm" title="Upload Materials">
                                                <i class="bi bi-upload"></i> Upload
                                            </a>
                                            <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" 
                                               class="btn btn-info btn-sm" title="View Materials">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
