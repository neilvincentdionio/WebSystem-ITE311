<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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
        <h4>Student System</h4>
        <div class="flex-grow-1">
            <a href="<?= base_url('student/dashboard') ?>" class="bg-dark">Dashboard</a>
        </div>
        <div>
            <a href="<?= base_url('auth/logout') ?>" class="mt-auto text-danger">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h3 class="mb-4">Welcome, <?= esc($user['name'] ?? 'Student') ?></h3>

        <!-- Enrolled Courses -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white">Enrolled Courses</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <ul class="list-group">
                        <?php foreach ($courses as $c): ?>
                            <li class="list-group-item">
                                <strong><?= esc($c['title']) ?></strong><br>
                                <small class="text-muted"><?= esc($c['description']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No enrolled courses yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white">Upcoming Deadlines</div>
            <div class="card-body">
                <?php if (!empty($deadlines)): ?>
                    <ul class="list-group">
                        <?php foreach ($deadlines as $d): ?>
                            <li class="list-group-item">
                                <strong><?= esc($d['course']) ?></strong> - <?= esc($d['task']) ?>
                                <span class="badge bg-danger float-end">Due: <?= esc($d['due']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No upcoming deadlines.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Grades -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">Recent Grades</div>
            <div class="card-body">
                <?php if (!empty($grades)): ?>
                    <ul class="list-group">
                        <?php foreach ($grades as $g): ?>
                            <li class="list-group-item">
                                <strong><?= esc($g['course']) ?></strong> - <?= esc($g['task']) ?>
                                <span class="badge bg-primary float-end"><?= esc($g['grade']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No grades available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
