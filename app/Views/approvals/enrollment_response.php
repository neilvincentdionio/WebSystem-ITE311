<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Course Enrollment Notification
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Enrollment Details</h6>
                        <p><strong>Course:</strong> <?= esc($enrollment['course_title']) ?></p>
                        <p><strong>Course Code:</strong> <?= esc($enrollment['course_code']) ?></p>
                        <p><strong>Credits/Units:</strong> <?= esc($enrollment['units']) ?></p>
                        <p><strong>Semester:</strong> <?= esc($enrollment['semester']) ?></p>
                        <p><strong>Term:</strong> <?= esc($enrollment['term']) ?></p>
                        <p><strong>Enrolled By:</strong> <?= esc($enrollment['enrolled_by_name'] ?? 'System Administrator') ?></p>
                        <p><strong>Enrolled On:</strong> <?= date('F j, Y \a\t g:i A', strtotime($enrollment['enrolled_at'])) ?></p>
                        <?php if ($enrollment['course_description']): ?>
                            <p><strong>Course Description:</strong> <?= esc($enrollment['course_description']) ?></p>
                        <?php endif; ?>
                    </div>

                    <form action="/approval/enrollment/submit/<?= $enrollment['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <h6>Your Response</h6>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="response" id="approve" value="approved" required>
                                <label class="form-check-label" for="approve">
                                    <strong class="text-success">Accept Enrollment</strong>
                                    <br>
                                    <small class="text-muted">I confirm that I want to enroll in this course and will complete all required coursework.</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="response" id="reject" value="rejected" required>
                                <label class="form-check-label" for="reject">
                                    <strong class="text-danger">Decline Enrollment</strong>
                                    <br>
                                    <small class="text-muted">I do not wish to enroll in this course at this time.</small>
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
                            <a href="/student/dashboard" class="btn btn-secondary">
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
