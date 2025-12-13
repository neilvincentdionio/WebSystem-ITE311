<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Teacher Assignment Notification
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Assignment Details</h6>
                        <p><strong>Course:</strong> <?= esc($assignment['course_title']) ?></p>
                        <p><strong>Course Code:</strong> <?= esc($assignment['course_code']) ?></p>
                        <p><strong>Assigned By:</strong> <?= esc($assignment['assigned_by_name'] ?? 'System Administrator') ?></p>
                        <p><strong>Assigned On:</strong> <?= date('F j, Y \a\t g:i A', strtotime($assignment['assigned_at'])) ?></p>
                        <?php if ($assignment['course_description']): ?>
                            <p><strong>Course Description:</strong> <?= esc($assignment['course_description']) ?></p>
                        <?php endif; ?>
                    </div>

                    <form action="/approval/assignment/submit/<?= $assignment['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <h6>Your Response</h6>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="response" id="approve" value="approved" required>
                                <label class="form-check-label" for="approve">
                                    <strong class="text-success">Accept Assignment</strong>
                                    <br>
                                    <small class="text-muted">I agree to teach this course and fulfill all teaching responsibilities.</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="response" id="reject" value="rejected" required>
                                <label class="form-check-label" for="reject">
                                    <strong class="text-danger">Decline Assignment</strong>
                                    <br>
                                    <small class="text-muted">I am unable to teach this course at this time.</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Optional Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" 
                                      placeholder="Add any comments or reasons for your decision..."></textarea>
                            <small class="text-muted">Maximum 500 characters</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/teacher/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Response
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
