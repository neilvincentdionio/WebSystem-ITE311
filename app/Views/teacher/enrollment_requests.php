<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Student Enrollment Requests</h1>
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

    <!-- Status Filter Buttons -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="<?= base_url('teacher/enrollment-requests/all') ?>" 
                   class="btn <?= $currentStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All Requests
                </a>
                <a href="<?= base_url('teacher/enrollment-requests/pending') ?>" 
                   class="btn <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                    Pending
                </a>
                <a href="<?= base_url('teacher/enrollment-requests/approved') ?>" 
                   class="btn <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
                    Approved
                </a>
                <a href="<?= base_url('teacher/enrollment-requests/rejected') ?>" 
                   class="btn <?= $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
                    Rejected
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($enrollments)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No enrollment requests found</h5>
                    <p class="text-muted">There are no <?= $currentStatus === 'all' ? '' : $currentStatus ?> student enrollment requests.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th>Response</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($enrollment['student_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($enrollment['course_title']) ?></strong><br>
                                        <small class="text-muted"><?= esc($enrollment['course_code']) ?></small>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y H:i', strtotime($enrollment['enrolled_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = match($enrollment['status']) {
                                            'pending' => 'bg-warning',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst(esc($enrollment['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($enrollment['responded_at']): ?>
                                            <small><?= date('M d, Y H:i', strtotime($enrollment['responded_at'])) ?></small><br>
                                            <?php if ($enrollment['response_message']): ?>
                                                <small class="text-muted"><?= esc($enrollment['response_message']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No response yet</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($enrollment['status'] === 'pending'): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <form action="<?= base_url('teacher/enrollment/accept') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                    <button type="submit" class="btn btn-success" title="Approve Enrollment">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="<?= base_url('teacher/enrollment/reject') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                    <button type="submit" class="btn btn-danger" title="Reject Enrollment">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <?php if ($enrollment['status'] === 'approved'): ?>
                                                    <i class="fas fa-check-circle text-success"></i> Approved
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle text-danger"></i> Rejected
                                                <?php endif; ?>
                                            </span>
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
