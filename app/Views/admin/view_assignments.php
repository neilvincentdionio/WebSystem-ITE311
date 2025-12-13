<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tasks me-2"></i>Teacher Assignments</h2>
        <div>
            <div class="btn-group" role="group">
                <a href="<?= base_url('admin/assignments?status=all') ?>" class="btn <?= $currentStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All (<?= count($assignments) ?>)
                </a>
                <a href="<?= base_url('admin/assignments?status=pending') ?>" class="btn <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                    Pending
                </a>
                <a href="<?= base_url('admin/assignments?status=approved') ?>" class="btn <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
                    Approved
                </a>
                <a href="<?= base_url('admin/assignments?status=rejected') ?>" class="btn <?= $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
                    Rejected
                </a>
            </div>
        </div>
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
            <?php if (empty($assignments)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No assignments found</h5>
                    <p class="text-muted">There are no <?= $currentStatus === 'all' ? '' : $currentStatus ?> teacher assignments.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Assigned By</th>
                                <th>Date Assigned</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($assignment['course_title']) ?></strong><br>
                                        <small class="text-muted"><?= esc($assignment['course_code']) ?></small>
                                    </td>
                                    <td>
                                        <?= esc($assignment['teacher_name']) ?><br>
                                        <small class="text-muted"><?= esc($assignment['teacher_email']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($assignment['day_range'] && $assignment['start_time'] && $assignment['end_time']): ?>
                                            <?= esc($assignment['day_range']) ?><br>
                                            <small class="text-muted">
                                                <?= date('h:i A', strtotime($assignment['start_time'])) ?> - <?= date('h:i A', strtotime($assignment['end_time'])) ?>
                                            </small><br>
                                            <?php if ($assignment['building'] && $assignment['room_number']): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> <?= esc($assignment['room_number']) ?>, <?= esc($assignment['building']) ?>
                                                </small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No schedule set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = match($assignment['status']) {
                                            'pending' => 'bg-warning',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst(esc($assignment['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        // Get admin name who assigned this
                                        $db = \Config\Database::connect();
                                        $admin = $db->table('users')->where('id', $assignment['assigned_by'])->get()->getRowArray();
                                        echo $admin ? esc($admin['name']) : 'Unknown';
                                        ?>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y H:i', strtotime($assignment['assigned_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($assignment['responded_at']): ?>
                                            <small><?= date('M d, Y H:i', strtotime($assignment['responded_at'])) ?></small><br>
                                            <?php if ($assignment['response_message']): ?>
                                                <small class="text-muted"><?= esc($assignment['response_message']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No response yet</span>
                                        <?php endif; ?>
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

<!-- Assignment Details Modal -->
<div class="modal fade" id="assignmentDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assignment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="assignmentDetailsContent">
                <!-- Content will be loaded via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewAssignmentDetails(assignmentId) {
    // Load assignment details via AJAX
    fetch(`<?= base_url('admin/assignments/details/') ?>${assignmentId}`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Course:</strong><br>
                        ${data.course_title}<br>
                        <small class="text-muted">${data.course_code}</small>
                    </div>
                    <div class="col-md-6">
                        <strong>Teacher:</strong><br>
                        ${data.teacher_name}<br>
                        <small class="text-muted">${data.teacher_email}</small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Schedule:</strong><br>
                        ${data.day_range || 'Not set'}<br>
                        ${data.start_time && data.end_time ? 
                            `${data.start_time} - ${data.end_time}` : 'Not set'}
                    </div>
                    <div class="col-md-6">
                        <strong>Location:</strong><br>
                        ${data.room_number && data.building ? 
                            `${data.room_number}, ${data.building}` : 'Not set'}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        <span class="badge bg-${data.status === 'pending' ? 'warning' : 
                            data.status === 'approved' ? 'success' : 'danger'}">
                            ${data.status.toUpperCase()}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Assigned:</strong><br>
                        ${data.assigned_at}
                    </div>
                </div>
                ${data.response_message ? `
                    <hr>
                    <div>
                        <strong>Response:</strong><br>
                        ${data.response_message}<br>
                        <small class="text-muted">${data.responded_at}</small>
                    </div>
                ` : ''}
            `;
            document.getElementById('assignmentDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('assignmentDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading assignment details:', error);
        });
}

function cancelAssignment(assignmentId, courseTitle) {
    if (confirm(`Are you sure you want to cancel the assignment for "${courseTitle}"?`)) {
        fetch(`<?= base_url('admin/assignments/cancel/') ?>${assignmentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the assignment.');
        });
    }
}

function reassignCourse(courseId, courseTitle) {
    if (confirm(`Do you want to reassign "${courseTitle}" to another teacher?`)) {
        window.location.href = `<?= base_url('admin/enrollments/assign/') ?>${courseId}`;
    }
}
</script>
