<?= $this->include('templates/header') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Enrolled Students - <?= esc($course['title']) ?></h2>
        <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Enrollments
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Course Information</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8">
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

    <?php if (!empty($course['teacher_name'])): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-secondary text-white">Teacher Schedule Details</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Teacher</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Term</th>
                            <th>Day Range</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?= esc($course['teacher_name']) ?></strong></td>
                            <td><?= esc($course['course_code']) ?></td>
                            <td><?= esc($course['semester'] ?? '-') ?></td>
                            <td><?= esc($course['term'] ?? '-') ?></td>
                            <td><?= esc($course['day_range'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($course['start_time']) && !empty($course['end_time'])): ?>
                                    <?= date('h:i A', strtotime($course['start_time'])) ?> - <?= date('h:i A', strtotime($course['end_time'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($course['room_number']) && !empty($course['building'])): ?>
                                    <?= esc($course['room_number']) ?> (<?= esc($course['building']) ?>)
                                    <?php if (!empty($course['room_capacity'])): ?>
                                        <br><small class="text-muted">Cap: <?= esc($course['room_capacity']) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($course['schedule_status'])): ?>
                                    <span class="badge bg-<?= $course['schedule_status'] === 'active' ? 'success' : ($course['schedule_status'] === 'inactive' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst(esc($course['schedule_status'])) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($enrolled_students)): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">Enrolled Students (<?= count($enrolled_students) ?>)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Semester</th>
                                <th>Term</th>
                                <th>Schedule</th>
                                <th>Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolled_students as $student): ?>
                                <tr>
                                    <td><?= esc($student['name']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= esc($student['semester'] ?? '-') ?></td>
                                    <td><?= esc($student['term'] ?? '-') ?></td>
                                    <td><?= esc($student['schedule'] ?? '-') ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($student['enrolled_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-warning text-white">No Students Enrolled</div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No students are currently enrolled in this course.
                </div>
                <a href="<?= base_url('admin/enrollments/course/' . $course['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Enroll Student
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
