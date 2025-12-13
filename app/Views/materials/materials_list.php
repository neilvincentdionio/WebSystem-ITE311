<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Materials for: <?= esc($course['title'] ?? ('Course #' . ($course['id'] ?? ''))) ?></h3>
        <a href="<?= base_url('materials') ?>" class="btn btn-secondary">Back to Materials</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($materials)): ?>
        <div class="alert alert-info">No materials uploaded yet.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($materials as $m): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold"><?= esc($m['file_name']) ?></div>
                        <small class="text-muted">Uploaded: <?= esc(date('Y-m-d H:i', strtotime($m['created_at']))) ?></small>
                    </div>
                    <div class="btn-group">
                        <a class="btn btn-primary btn-sm" href="<?= base_url('materials/download/' . $m['id']) ?>">Download</a>
                        <?php if (in_array(strtolower(session()->get('role')), ['admin','teacher'])): ?>
                            <a class="btn btn-outline-danger btn-sm" href="<?= base_url('materials/delete/' . $m['id']) ?>" onclick="return confirm('Delete this material?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
</html>
