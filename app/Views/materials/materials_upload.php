<?= $this->include('templates/header') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material - <?= esc($course['title'] ?? ('Course #' . ($course['id'] ?? ''))) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Upload Material for: <?= esc($course['title'] ?? ('Course #' . ($course['id'] ?? ''))) ?>
        </div>
        <div class="card-body">
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success"><?= esc($success) ?></div>
            <?php endif; ?>
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger"><?= esc($error) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="<?= base_url('course/' . $course['id'] . '/upload') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="exam_type" class="form-label">Exam Type</label>
                    <select class="form-select" id="exam_type" name="exam_type" required>
                        <option value="">Select exam type...</option>
                        <option value="prelim">Prelim</option>
                        <option value="midterm">Midterm</option>
                        <option value="finals">Finals</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="material" class="form-label">Choose file</label>
                    <input class="form-control" type="file" id="material" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z,.txt,.jpg,.jpeg,.png" required>
                    <div class="form-text">Allowed: pdf, ppt, pptx, doc, docx, xls, xlsx, zip, rar. Max 20MB.</div>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
