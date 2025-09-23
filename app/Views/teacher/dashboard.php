<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
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
            display: block;
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
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <h4>Teacher System</h4>
        <div class="flex-grow-1">
            <a href="<?= base_url('teacher/dashboard') ?>"class="bg-dark">Dashboard</a>
            <a href="<?= base_url('teacher/create-course') ?>">Create Course</a>
        </div>
        <div>
            <a href="<?= base_url('auth/logout') ?>"class="mt-auto text-danger">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h3 class="mb-4">Welcome, <?= esc($user['name'] ?? 'Teacher') ?></h3>
        <div class="row">
            <!-- Courses Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">Your Courses</div>
                    <div class="card-body">
                        <?php if (!empty($courses)): ?>
                            <ul class="list-group">
                                <?php foreach ($courses as $course): ?>
                                    <li class="list-group-item">
                                        <strong><?= esc($course['title']) ?></strong><br>
                                        <small class="text-muted"><?= esc($course['description']) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">You are not teaching any courses yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">Notifications</div>
                    <div class="card-body">
                        <?php if (!empty($notifications)): ?>
                            <ul class="list-group">
                                <?php foreach ($notifications as $n): ?>
                                    <li class="list-group-item"><?= esc($n['message']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No new notifications.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <a href="<?= base_url('teacher/create-course') ?>" class="btn btn-success">
             + Create New Course
        </a>
    </div>
</body>
</html>
