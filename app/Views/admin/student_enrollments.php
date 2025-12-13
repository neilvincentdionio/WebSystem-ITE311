<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Student Enrollments</h1>
                    <p class="text-muted">Manage student enrollment requests and track their status</p>
                </div>
                <div>
                    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('admin/student-enrollments') ?>" 
                           class="btn <?= $currentStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            All (<?= $counts['all'] ?>)
                        </a>
                        <a href="<?= base_url('admin/student-enrollments/pending') ?>" 
                           class="btn <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                            Pending (<?= $counts['pending'] ?>)
                        </a>
                        <a href="<?= base_url('admin/student-enrollments/approved') ?>" 
                           class="btn <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
                            Approved (<?= $counts['approved'] ?>)
                        </a>
                        <a href="<?= base_url('admin/student-enrollments/rejected') ?>" 
                           class="btn <?= $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
                            Rejected (<?= $counts['rejected'] ?>)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
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

    <!-- Enrollments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-check me-2"></i>
                        Student Enrollment Requests
                        <span class="badge bg-secondary float-end"><?= count($enrollments) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($enrollments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Enrolled By</th>
                                        <th>Enrolled Date</th>
                                        <th>Response Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?= esc($enrollment['student_name']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($enrollment['course_code']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($enrollment['course_title']) ?></small>
                                                    <?php if ($enrollment['course_semester'] || $enrollment['course_term']): ?>
                                                        <br>
                                                        <small class="text-info">
                                                            <?= esc($enrollment['course_semester'] ?? '') ?> 
                                                            <?= esc($enrollment['course_term'] ?? '') ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = match($enrollment['status']) {
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $statusIcon = match($enrollment['status']) {
                                                    'pending' => 'bi-hourglass-split',
                                                    'approved' => 'bi-check-circle',
                                                    'rejected' => 'bi-x-circle',
                                                    default => 'bi-question-circle'
                                                };
                                                $statusText = match($enrollment['status']) {
                                                    'pending' => 'Pending Student Response',
                                                    'approved' => 'Approved',
                                                    'rejected' => 'Rejected',
                                                    default => 'Unknown'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <i class="bi <?= $statusIcon ?>"></i> <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= esc($enrollment['enrolled_by_name'] ?? 'Admin') ?>
                                            </td>
                                            <td>
                                                <small><?= date('M j, Y h:i A', strtotime($enrollment['enrolled_at'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($enrollment['responded_at']): ?>
                                                    <small><?= date('M j, Y h:i A', strtotime($enrollment['responded_at'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($enrollment['status'] === 'pending'): ?>
                                                        <button class="btn btn-outline-info btn-sm" 
                                                                onclick="viewDetails(<?= $enrollment['id'] ?>)"
                                                                title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <form action="<?= base_url('admin/student-enrollments/approve') ?>" 
                                                              method="post" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Approve this student enrollment?')">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                            <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form action="<?= base_url('admin/student-enrollments/reject') ?>" 
                                                              method="post" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Reject this student enrollment?')">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form action="<?= base_url('admin/enrollments/remove/' . $enrollment['id']) ?>" 
                                                          method="post" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to remove this enrollment?')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if ($enrollment['status'] === 'pending' && $enrollment['response_message']): ?>
                                            <tr class="table-light">
                                                <td colspan="7">
                                                    <div class="alert alert-info mb-0 py-2">
                                                        <small>
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            <strong>Student Response:</strong> <?= esc($enrollment['response_message']) ?>
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-person-check fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Enrollment Requests Found</h5>
                            <p class="text-muted">
                                <?php if ($currentStatus === 'pending'): ?>
                                    There are no pending enrollment requests at the moment.
                                <?php elseif ($currentStatus === 'approved'): ?>
                                    No enrollments have been approved yet.
                                <?php elseif ($currentStatus === 'rejected'): ?>
                                    No enrollments have been rejected.
                                <?php else: ?>
                                    No enrollment requests have been created yet.
                                <?php endif; ?>
                            </p>
                            <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Enroll Student
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enrollment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Enrollment details would be displayed here.</p>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetails(enrollmentId) {
    // You can implement a modal to show detailed information
    // For now, just show an alert
    alert('Enrollment ID: ' + enrollmentId + '\n\nDetailed view can be implemented here.');
}
</script>

<?= $this->include('templates/footer') ?>
