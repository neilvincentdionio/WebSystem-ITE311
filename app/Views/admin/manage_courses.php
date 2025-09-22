<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
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

    <!-- Sidebar -->
    <?= $this->include('admin/reusable/sidebar') ?>

    <!-- Content -->
    <div class="content">
        <h3 class="mb-4">Manage Courses</h3>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Instructor</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td><?= esc($c['id']) ?></td>
                                    <td><?= esc($c['title']) ?></td>
                                    <td><?= esc($c['description']) ?></td>
                                    <td><?= esc($c['instructor_name'] ?? $c['instructor_id']) ?></td>
                                    <td><?= esc($c['created_at']) ?></td>
                                    <td><?= esc($c['updated_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No courses found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
