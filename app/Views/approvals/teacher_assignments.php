<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>My Teaching Assignments</h3>
        <div class="text-muted">
            <small>Manage your course assignments and responses</small>
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

    <!-- Pending Assignments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Pending Assignments
                        <span class="badge bg-secondary float-end">
                            <?= count(array_filter($assignments, fn($a) => $a['status'] === 'pending')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $pendingAssignments = array_filter($assignments, fn($a) => $a['status'] === 'pending');
                    if (empty($pendingAssignments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No pending assignments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($pendingAssignments as $assignment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($assignment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($assignment['course_code']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Assigned: <?= date('F j, Y', strtotime($assignment['assigned_at'])) ?>
                                            </small></p>
                                        </div>
                                        <div class="ms-3">
                                            <a href="/approval/assignment/respond/<?= $assignment['id'] ?>" 
                                               class="btn btn-warning btn-sm">
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

    <!-- Approved Assignments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Approved Assignments
                        <span class="badge bg-light text-success float-end">
                            <?= count(array_filter($assignments, fn($a) => $a['status'] === 'approved')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $approvedAssignments = array_filter($assignments, fn($a) => $a['status'] === 'approved');
                    if (empty($approvedAssignments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No approved assignments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($approvedAssignments as $assignment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($assignment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($assignment['course_code']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Approved: <?= date('F j, Y \a\t g:i A', strtotime($assignment['responded_at'])) ?>
                                            </small></p>
                                            <?php if ($assignment['response_message']): ?>
                                                <p class="mb-0"><small class="text-muted">
                                                    <strong>Your message:</strong> <?= esc($assignment['response_message']) ?>
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

    <!-- Rejected Assignments -->
    <div class="row">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Rejected Assignments
                        <span class="badge bg-light text-danger float-end">
                            <?= count(array_filter($assignments, fn($a) => $a['status'] === 'rejected')) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $rejectedAssignments = array_filter($assignments, fn($a) => $a['status'] === 'rejected');
                    if (empty($rejectedAssignments)): 
                    ?>
                        <p class="text-muted text-center mb-0">No rejected assignments</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($rejectedAssignments as $assignment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($assignment['course_title']) ?></h6>
                                            <p class="mb-1"><strong>Code:</strong> <?= esc($assignment['course_code']) ?></p>
                                            <p class="mb-1"><small class="text-muted">
                                                Rejected: <?= date('F j, Y \a\t g:i A', strtotime($assignment['responded_at'])) ?>
                                            </small></p>
                                            <?php if ($assignment['response_message']): ?>
                                                <p class="mb-0"><small class="text-muted">
                                                    <strong>Your message:</strong> <?= esc($assignment['response_message']) ?>
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
