<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 200px;
            background-color: #3a3a3bff;
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar h4 {
            color: #fff;
            margin-bottom: 30px;
            text-align: center;
        }
        .sidebar .nav-link {
            color: white;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
        }
        .sidebar .nav-link:hover {
            background-color: #0b5ed7;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4>LMS DASHBOARD</h4>
        <a href="<?= base_url('dashboard') ?>" class="nav-link">Dashboard</a>
        <a href="<?= base_url('auth/logout') ?>" class="nav-link ">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h3>Welcome, <?= esc($user['name']) ?> </h3>
        <p class="text-muted">You are logged in as <span class="badge bg-info"><?= ucfirst(esc($user['role'])) ?></span></p>

        <div class="card mt-4">
            <div class="card-header">
                <h5>User Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= esc($user['name']) ?></p>
                <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
                <p><strong>Role:</strong> <span class="badge bg-secondary"><?= ucfirst(esc($user['role'])) ?></span></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
