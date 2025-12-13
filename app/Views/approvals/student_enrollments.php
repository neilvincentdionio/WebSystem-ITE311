<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>My Course Enrollments</h3>
        <div class="text-muted">
            <small>Manage your course enrollments and responses</small>
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

    <!-- Pending Enrollments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Pending Enrollments
                        <span class="badge bg-light float-end">
                            <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'pending')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $pendingEnrollments = array_filter($enrollments, fn($e) => $e['status'] === 'pending');
                    if (empty($pendingEnrollments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No pending enrollments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($pendingEnrollments as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($enrollment['course_code']) ?></p>
                                            <p class="mb-1"><strong>Units:</strong> <?= esc($enrollment['units']) ?></p>
                                            <p class="mb-1"><strong>Semester:</strong> <?= esc($enrollment['semester']) ?> | <strong>Term:</strong> <?= esc($enrollment['term']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Enrolled: <?= date('F j, Y', strtotime($enrollment['enrolled_at'])) ?>
                                            </small></p>
                                        </div>
                                        <div class="ms-3">
                                            <a href="/approval/enrollment/respond/<?= $enrollment['id'] ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-reply me-1"></i>Respond Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Enrollments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Active Enrollments
                        <span class="badge bg-light text-success float-end">
                            <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'approved')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $approvedEnrollments = array_filter($enrollments, fn($e) => $e['status'] === 'approved');
                    if (empty($approvedEnrollments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No active enrollments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($approvedEnrollments as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($enrollment['course_code']) ?></p>
                                            <p class="mb-1"><strong>Units:</strong> <?= esc($enrollment['units']) ?></p>
                                            <p class="mb-1"><strong>Semester:</strong> <?= esc($enrollment['semester']) ?> | <strong>Term:</strong> <?= esc($enrollment['term']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Approved: <?= date('F j, Y \a\t g:i A', strtotime($enrollment['responded_at'])) ?>
                                            </small></p>
                                            <?php if ($enrollment['response_message']): ?>
                                                <p class="mb-0"><small class="text-muted">
                                                    <strong>Your message:</strong> <?= esc($enrollment['response_message']) ?>
                                                </small></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected Enrollments -->
    <div class="row">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Rejected Enrollments
                        <span class="badge bg-light text-danger float-end">
                            <?= count(array_filter($enrollments, fn($e) => $e['status'] === 'rejected')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $rejectedEnrollments = array_filter($enrollments, fn($e) => $e['status'] === 'rejected');
                    if (empty($rejectedEnrollments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No rejected enrollments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($rejectedEnrollments as $enrollment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($enrollment['course_code']) ?></p>
                                            <p class="mb-1"><strong>Units:</strong> <?= esc($enrollment['units']) ?></p>
                                            <p class="mb-1"><strong>Sester:</strong> <?= esc($enrollment['semester']) ?> | <strong>Term:</strong> <?= esc($enrollment['term']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Rejected: <?= date('F j, Y \a\t g:i A', strtotime($enrollment['responded_at'])) ?>
                                            </small></p>
                                            <?php if ($enrollment['response_message']): ?>
                                                <p class="mb-0"><small class="text-muted">
                                                    <strong>Your message:</strong> <?= esc($enrollment['response_message']) ?>
                                                </small></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <span class="badge bg-danger">Declined</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
