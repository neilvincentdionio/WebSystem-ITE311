<?= $this->include('templates/header') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Materials - <?= esc($course['title'] ?? ('Course #' . ($course['id'] ?? ''))) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Materials for: <?= esc($course['title'] ?? ('Course #' . ($course['id'] ?? ''))) ?></h3>
        <?php $role = strtolower((string) (session()->get('role') ?? '')); $backUrl = ($role === 'admin') ? 'admin/dashboard' : 'dashboard'; ?>
        <a href="<?= base_url($backUrl) ?>" class="btn btn-secondary">Back</a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
