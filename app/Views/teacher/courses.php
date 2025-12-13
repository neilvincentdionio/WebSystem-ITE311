<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Courses</h1>
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
            <h5 class="card-title mb-0">Courses Assigned to Me</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Schedule</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['title'] ?? 'N/A') ?></td>
                                    <td><?= esc(substr($course['description'] ?? '', 0, 100)) ?>...</td>
                                    <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['academic_year'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#enrollStudentModal"
                                                data-course-id="<?= $course['id'] ?>"
                                                data-course-title="<?= esc($course['title']) ?>"
                                                title="Enroll Student">
                                            <i class="bi bi-person-plus me-1"></i> Enroll Student
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Courses Assigned</h5>
                    <p class="text-muted">You haven't been assigned to any courses yet.</p>
                    <p class="text-muted">Please contact the administrator to get course assignments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollStudentModalLabel">Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('teacher/enroll-student') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="courseTitle" class="form-label">Course</label>
                        <input type="text" class="form-control" id="courseTitle" readonly>
                        <input type="hidden" name="course_id" id="courseId">
                    </div>
                    <div class="mb-3">
                        <label for="studentSelect" class="form-label">Select Student *</label>
                        <select class="form-select" id="studentSelect" name="student_id" required>
                            <option value="">Choose a student...</option>
                            <?php if (isset($students) && is_array($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>"><?= esc($student['name']) ?> (<?= esc($student['email']) ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="enrollmentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="enrollmentNotes" name="notes" rows="3" 
                                  placeholder="Add any notes about this enrollment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus me-1"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollStudentModal = document.getElementById('enrollStudentModal');
    enrollStudentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const courseId = button.getAttribute('data-course-id');
        const courseTitle = button.getAttribute('data-course-title');
        
        const modalCourseId = document.getElementById('courseId');
        const modalCourseTitle = document.getElementById('courseTitle');
        
        modalCourseId.value = courseId;
        modalCourseTitle.value = courseTitle;
    });
});
</script>

</body>
</html>
