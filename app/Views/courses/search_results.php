<?= $this->include('templates/header') ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Course Search Results</h2>
            
            <!-- Search Form -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="<?= base_url('course/search') ?>" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" 
                                   class="form-control" 
                                   name="search_term" 
                                   id="search_term" 
                                   placeholder="Search courses by title or description..." 
                                   value="<?= esc($searchTerm ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <?php if (!empty($searchTerm)): ?>
                            Search Results for: "<?= esc($searchTerm) ?>"
                        <?php else: ?>
                            All Courses
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No courses found.
                            <?php if (!empty($searchTerm)): ?>
                                Try a different search term.
                            <?php else: ?>
                                No courses available in the system.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">ID</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?= esc($course['id']) ?></td>
                                            <td>
                                                <strong><?= esc($course['title']) ?></strong>
                                            </td>
                                            <td>
                                                <?php 
                                                $description = esc($course['description'] ?? 'No description available');
                                                // Highlight search term if present
                                                if (!empty($searchTerm) && stripos($description, $searchTerm) !== false) {
                                                    $description = preg_replace(
                                                        '/(' . preg_quote($searchTerm, '/') . ')/i',
                                                        '<mark>$1</mark>',
                                                        $description
                                                    );
                                                }
                                                echo $description;
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $session = session();
                                                if ($session->get('isLoggedIn')): 
                                                    $role = strtolower((string)$session->get('role'));
                                                    if ($role === 'admin'): ?>
                                                        <a class="btn btn-sm btn-primary" 
                                                           href="<?= base_url('course/' . $course['id'] . '/upload') ?>">
                                                            <i class="bi bi-upload"></i> Upload
                                                        </a>
                                                    <?php elseif ($role === 'student'): ?>
                                                        <button class="btn btn-sm btn-success enrollBtn" 
                                                                data-course-id="<?= $course['id'] ?>">
                                                            <i class="bi bi-plus-circle"></i> Enroll
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a class="btn btn-sm btn-outline-primary" 
                                                       href="<?= base_url('auth/login') ?>">
                                                        Login to Enroll
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3 text-muted">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Found <?= count($courses) ?> course(s)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-3">
                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Script (for students) -->
<?php if (session()->get('isLoggedIn') && strtolower((string)session()->get('role')) === 'student'): ?>
<script>
$(document).ready(function() {
    $('.enrollBtn').click(function(e) {
        e.preventDefault();
        let button = $(this);
        let courseId = button.data('course-id');
        let courseTitle = button.closest('tr').find('td:nth-child(2)').text().trim();

        $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
            let alertType = response.success ? 'success' : 'danger';
            let alertHtml = '<div class="alert alert-' + alertType + ' alert-dismissible fade show" role="alert">' +
                          response.message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>';
            
            $('.container').first().prepend(alertHtml);
            
            if (response.success) {
                button.prop('disabled', true).text('Enrolled').removeClass('btn-success').addClass('btn-secondary');
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
<?php endif; ?>

