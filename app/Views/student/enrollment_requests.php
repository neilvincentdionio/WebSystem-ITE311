<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Enrollment Requests</h1>
        <a href="<?= base_url('student/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
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
            <h5 class="card-title mb-0">My Enrollment Requests</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($enrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Teacher</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Teacher Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?= esc($enrollment['course_code']) ?></td>
                                    <td>
                                        <strong><?= esc($enrollment['course_title']) ?></strong><br>
                                        <small class="text-muted"><?= esc(substr($enrollment['description'] ?? '', 0, 100)) ?>...</small>
                                    </td>
                                    <td><?= esc($enrollment['teacher_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($enrollment['enrolled_at'])) ?></td>
                                    <td>
                                        <?php if ($enrollment['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($enrollment['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($enrollment['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= esc($enrollment['response_message'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($enrollment['status'] === 'pending'): ?>
                                            <div class="btn-group btn-group-sm">
                                                <form method="post" action="<?= base_url('student/accept-enrollment') ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Accept this enrollment request?')">
                                                        Accept
                                                    </button>
                                                </form>
                                                <form method="post" action="<?= base_url('student/reject-enrollment') ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this enrollment request?')">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <?php if ($enrollment['status'] === 'approved'): ?>
                                                    Accepted
                                                <?php elseif ($enrollment['status'] === 'rejected'): ?>
                                                    Rejected
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Enrollment Summary</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="badge bg-warning">Pending: <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'pending')) ?></span>
                                </div>
                                <div class="col-md-4">
                                    <span class="badge bg-success">Approved: <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'approved')) ?></span>
                                </div>
                                <div class="col-md-4">
                                    <span class="badge bg-danger">Rejected: <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'rejected')) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-envelope-open fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Enrollment Requests</h5>
                    <p class="text-muted">You don't have any enrollment requests at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
