<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

    <!-- Include the dynamic navigation -->
    <?= $this->include('templates/header') ?>

    <div class="container mt-4">
        <h2 class="mb-4">Welcome, <?= esc($user['name']) ?>!</h2>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Role: <?= ucfirst(esc($role)) ?></h5>
                <p class="card-text"><?= esc($roleData['message']) ?></p>
            </div>
        </div>

        <!-- Role-specific sections -->
        <?php if ($role === 'admin'): ?>
            <div class="card mb-4">
                <div class="card-header">System Overview</div>
                <div class="card-body">
                    <p>Total Users: <?= count($roleData['users']) ?></p>
                    <ul>
                        <?php foreach ($roleData['users'] as $u): ?>
                            <li><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        <?php elseif ($role === 'teacher'): ?>
            <div class="card mb-4">
                <div class="card-header">My Courses</div>
                <div class="card-body">
                    <?php if (empty($roleData['myCourses'])): ?>
                        <p class="text-muted">No courses found.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($roleData['myCourses'] as $course): ?>
                                <li class="list-group-item"><?= esc($course['title']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($role === 'student'): ?>
            <!-- Enrolled Courses -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">My Enrolled Courses</div>
                <div class="card-body" id="enrolledCourses">
                    <?php if (empty($roleData['enrolledCourses'])): ?>
                        <p class="text-muted">You are not enrolled in any courses yet.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($roleData['enrolledCourses'] as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= esc($course['title']) ?></span>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= base_url('course/' . $course['id'] . '/materials') ?>">Materials</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Courses -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Available Courses
                </div>
                <div class="card-body" id="availableCourses">
                    <?php if (empty($roleData['availableCourses'])): ?>
                        <p class="text-muted">No courses available at the moment.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($roleData['availableCourses'] as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="course-title"><?= esc($course['title']) ?></span>
                                    <button class="btn btn-success btn-sm enrollBtn" data-course-id="<?= $course['id'] ?>">Enroll</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>

<script>
$(document).ready(function() {
    $('.enrollBtn').click(function(e) {
        e.preventDefault();

        let button = $(this);
        let courseId = button.data('course-id');
        let courseTitle = button.closest('li').find('.course-title').text();

        $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {

            // Show Bootstrap alert
            let alertType = response.success ? 'success' : 'danger';
            let alertHtml = `
                <div class="alert alert-${alertType} alert-dismissible fade show mt-3" role="alert">
                    ${response.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            $('.container').prepend(alertHtml);

            if (response.success) {
                // Disable or hide button
                button.prop('disabled', true).text('Enrolled');

                // Add to enrolled list
                if ($('#enrolledCourses ul').length === 0) {
                    $('#enrolledCourses').html('<ul class="list-group"></ul>');
                }
                $('#enrolledCourses ul').append(`<li class="list-group-item">${courseTitle}</li>`);

                // Remove from available
                button.closest('li').remove();
            }

        }, 'json').fail(function() {
            $('.container').prepend(`
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    An error occurred. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`);
        });
    });
});
</script>

</body>
</html>
