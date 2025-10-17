 <?= $this->include('templates/header') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">📢 Announcements</h2>

        <?php if (!empty($announcements) && is_array($announcements)): ?>
            <div class="list-group">
                <?php foreach ($announcements as $announce): ?>
                    <div class="list-group-item mb-3 shadow-sm rounded">
                        <h5 class="fw-bold"><?= esc($announce['title']) ?></h5>
                        <p class="mb-2"><?= esc($announce['content']) ?></p>
                        <small class="text-muted">
                            Posted on: <?= date('F j, Y g:i A', strtotime($announce['created_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No announcements available.
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
