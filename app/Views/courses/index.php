<?= $this->include('templates/header') ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Courses</h2>
            
            <!-- Search Form (Step 4) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form id="searchForm" class="d-flex">
                        <div class="input-group">
                            <input type="text" 
                                   id="searchInput" 
                                   class="form-control"
                                   placeholder="Search courses..." 
                                   name="search_term">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses Listing (Step 6: Card-based structure) -->
            <div id="coursesContainer" class="row">
                <?php if (empty($courses)): ?>
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> No courses available at the moment.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card course-card h-100 shadow-sm" 
                                 data-course-title="<?= esc(strtolower($course['title'])) ?>" 
                                 data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                    <p class="card-text flex-grow-1"><?= esc($course['description'] ?? 'No description available') ?></p>
                                    <div class="mt-auto">
                                        <?php 
                                        $session = session();
                                        if ($session->get('isLoggedIn')): 
                                            $role = strtolower((string)$session->get('role'));
                                            if ($role === 'admin'): ?>
                                                <a class="btn btn-primary w-100" 
                                                   href="<?= base_url('course/' . $course['id'] . '/upload') ?>">
                                                    <i class="bi bi-upload"></i> Upload Materials
                                                </a>
                                            <?php elseif ($role === 'student'): ?>
                                                <a class="btn btn-info w-100" 
                                                   href="<?= base_url('course/' . $course['id']) ?>">
                                                    <i class="bi bi-eye"></i> View Course Details
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a class="btn btn-outline-primary w-100" 
                                               href="<?= base_url('auth/login') ?>">
                                                <i class="bi bi-box-arrow-in-right"></i> Login to Enroll
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="col-12 mt-3">
                        <div class="text-muted">
                            <small class="course-count">
                                <i class="bi bi-info-circle"></i> 
                                Showing <?= count($courses) ?> course(s)
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Search Script with Client-Side Filtering (Step 5) -->
<script>
$(document).ready(function() {
    // Client-side filtering (instant search on keyup) - Updated for card structure
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.course-card').filter(function() {
            var title = $(this).data('course-title') || '';
            var description = $(this).data('course-description') || '';
            var matches = title.indexOf(value) > -1 || description.indexOf(value) > -1;
            // Toggle the parent col-md-4 div
            $(this).closest('.col-md-4').toggle(matches);
        });
        
        // Update course count
        var visibleCount = $('.course-card:visible').length;
        var totalCount = $('.course-card').length;
        if ($('.course-count').length) {
            $('.course-count').text('Showing ' + visibleCount + ' of ' + totalCount + ' course(s)');
        }
    });
    
    // Server-side search with AJAX (on form submission) - Updated for card structure
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        const searchTerm = $('#searchInput').val().trim();
        const $container = $('#coursesContainer');
        
        // Show loading state
        $container.html('<div class="col-12"><div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Searching courses...</p></div></div>');
        
        // Make AJAX request to search endpoint
        $.ajax({
            url: '<?= base_url('courses/search') ?>',
            method: 'GET',
            data: { search_term: searchTerm },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                // Handle structured JSON response
                const courses = response.results || response; // Support both formats
                const count = response.count !== undefined ? response.count : courses.length;
                const term = response.term !== undefined ? response.term : searchTerm;
                
                if (!courses || courses.length === 0) {
                    $container.html(
                        '<div class="col-12">' +
                        '<div class="alert alert-danger mb-0">' +
                        '<i class="bi bi-exclamation-triangle"></i> No courses found matching "' + 
                        (term || 'your search') + '".' +
                        '</div></div>'
                    );
                } else {
                    let html = '';
                    
                    courses.forEach(function(course) {
                        const description = course.description || 'No description available';
                        html += '<div class="col-md-4 mb-4">' +
                               '<div class="card course-card h-100 shadow-sm" ' +
                               'data-course-title="' + escapeHtml(course.title.toLowerCase()) + '" ' +
                               'data-course-description="' + escapeHtml(description.toLowerCase()) + '">' +
                               '<div class="card-body d-flex flex-column">' +
                               '<h5 class="card-title">' + escapeHtml(course.title) + '</h5>' +
                               '<p class="card-text flex-grow-1">' + escapeHtml(description) + '</p>' +
                               '<div class="mt-auto">';
                        
                        <?php if (session()->get('isLoggedIn')): ?>
                            <?php if (strtolower((string)session()->get('role')) === 'admin'): ?>
                                html += '<a class="btn btn-primary w-100" href="<?= base_url('course/') ?>' + course.id + '/upload">' +
                                       '<i class="bi bi-upload"></i> Upload Materials</a>';
                            <?php elseif (strtolower((string)session()->get('role')) === 'student'): ?>
                                html += '<a class="btn btn-info w-100" href="<?= base_url('course/') ?>' + course.id + '">' +
                                       '<i class="bi bi-eye"></i> View Course Details</a>';
                            <?php endif; ?>
                        <?php else: ?>
                            html += '<a class="btn btn-outline-primary w-100" href="<?= base_url('auth/login') ?>">' +
                                   '<i class="bi bi-box-arrow-in-right"></i> Login to Enroll</a>';
                        <?php endif; ?>
                        
                        html += '</div></div></div></div>';
                    });
                    
                    html += '<div class="col-12 mt-3">' +
                           '<div class="text-muted">' +
                           '<small class="course-count"><i class="bi bi-info-circle"></i> Found ' + count + ' course(s)' +
                           (term ? ' for "' + escapeHtml(term) + '"' : '') +
                           '</small></div></div>';
                    
                    $container.html(html);
                    
                    // Re-attach enrollment handlers for new buttons
                    attachEnrollmentHandlers();
                    
                    // Re-attach client-side filtering for new cards
                    $('#searchInput').trigger('keyup');
                }
            },
            error: function(xhr, status, error) {
                $container.html(
                    '<div class="alert alert-danger mb-0">' +
                    '<i class="bi bi-exclamation-triangle"></i> An error occurred while searching. Please try again.' +
                    '</div>'
                );
                console.error('Search error:', error);
            }
        });
    });
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }
    
    // Function to attach enrollment handlers - Updated for card structure
    function attachEnrollmentHandlers() {
        $('.enrollBtn').off('click').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const courseId = button.data('course-id');
            const courseTitle = button.data('course-title') || button.closest('.card').find('.card-title').text().trim();

            $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
                const alertType = response.success ? 'success' : 'danger';
                const alertHtml = '<div class="alert alert-' + alertType + ' alert-dismissible fade show" role="alert">' +
                                response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                '</div>';
                
                $('.container').first().prepend(alertHtml);
                
                if (response.success) {
                    button.prop('disabled', true)
                          .html('<i class="bi bi-check-circle"></i> Enrolled')
                          .removeClass('btn-success')
                          .addClass('btn-secondary');
                }
            }).fail(function() {
                alert('An error occurred. Please try again.');
            });
        });
    }
    
    // Initial attachment for existing buttons
    attachEnrollmentHandlers();
});
</script>
