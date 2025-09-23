<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Course</title>
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
            <a href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('teacher/create-course') ?>" class="bg-dark">Create Course</a>
        </div>
        <div>
             <a href="<?= base_url('auth/logout') ?>"class="mt-auto text-danger">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h3 class="mb-4">Create a New Course</h3>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="<?= base_url('teacher/create-course') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Course Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Course Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" required></textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-success"> Save Course</button>
                    <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-secondary"> Back</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
