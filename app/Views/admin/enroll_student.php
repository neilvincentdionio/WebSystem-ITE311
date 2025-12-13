<?= $this->include('templates/header') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Enroll Student in Course</h2>
        <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Enrollments
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> Please fix the following issues:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Course Information</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5><?= esc($course['title']) ?></h5>
                    <p class="text-muted">
                        <strong>Course Code:</strong> <?= esc($course['course_code']) ?> | 
                        <strong>Department:</strong> <?= esc($course['department']) ?> | 
                        <strong>Program:</strong> <?= esc($course['program']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-dark text-white">Enroll Student</div>
        <div class="card-body">
            <form action="<?= base_url('admin/enrollments/store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" value="<?= esc($course['id']) ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Student *</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Student</option>
                                <?php if (isset($students) && is_array($students)): ?>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= esc($student['id']) ?>" <?= old('user_id') == $student['id'] ? 'selected' : '' ?>>
                                            <?= esc($student['name']) ?> (<?= esc($student['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Select a student to enroll in this course. Schedule information is managed through the teacher assignment system.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($enrolled_students)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">Currently Enrolled Students (<?= count($enrolled_students) ?>)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Enrolled Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolled_students as $student): ?>
                                <tr>
                                    <td><?= esc($student['name']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($student['enrolled_at'])) ?></td>
                                    <td>
                                        <form action="<?= base_url('admin/enrollments/remove/' . $student['enrollment_id']) ?>" method="post" onsubmit="return confirm('Are you sure you want to remove this student from the course?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-user-minus"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>