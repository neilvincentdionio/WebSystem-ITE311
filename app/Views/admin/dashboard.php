<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar h4 {
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: background 0.2s;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            flex-grow: 1;
            padding: 30px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
   <div class="sidebar d-flex flex-column">
    <h4 class="p-3 border-bottom">Admin System</h4>
    <a href="<?= site_url('admin/dashboard') ?>" class="bg-dark">Dashboard</a>
    <a href="<?= site_url('admin/users') ?>">Manage Users</a>
    <a href="<?= site_url('admin/courses') ?>">Manage Courses</a>
    <a href="<?= site_url('admin/logout') ?>" class="mt-auto text-danger">Logout</a>
</div>
    <!-- Content -->
    <div class="content">
        <h3 class="mb-4">Welcome, <?= esc($user['name'] ?? 'Admin') ?></h3>

        <div class="row">
            <!-- Total Users card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <p class="display-6"><?= esc($statistics['total_users']) ?></p>
                       <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-sm">Manage Users</a>
                    </div>
                </div>
            </div>

            <!-- Total Courses card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5>Total Courses</h5>
                        <p class="display-6"><?= esc($statistics['total_courses']) ?></p>
                        <a href="<?= base_url('admin/courses') ?>" class="btn btn-primary btn-sm">Manage Courses</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-dark text-white">
                Recent Activity
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activity)): ?>
                    <ul class="list-group">
                        <?php foreach ($recent_activity as $activity): ?>
                            <li class="list-group-item">
                                <strong><?= esc($activity['user']) ?></strong> <?= esc($activity['action']) ?>
                                <small class="text-muted float-end"><?= esc($activity['created_at']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No recent activity found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
