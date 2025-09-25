<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Include the dynamic navigation -->
    <?= $this->include('templates/header') ?>

    <div class="container mt-4">
        <h2 class="mb-4">Welcome, <?= esc($user['name']) ?>!</h2>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Role: <?= ucfirst(esc($role)) ?></h5>
                <p class="card-text"><?= esc($roleData['message']) ?></p>
            </div>
        </div>

        <!-- Role-specific sections -->
        <?php if ($role === 'admin'): ?>
            <!-- System Overview -->
            <div class="card mb-4">
                <div class="card-header">System Overview</div>
                <div class="card-body">
                    <p>Total Users: <?= count($roleData['users']) ?></p>
                    <ul>
                        <?php foreach ($roleData['users'] as $u): ?>
                            <li><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        <?php elseif ($role === 'teacher'): ?>
            <!-- Teacher: My Courses -->
            <div class="card mb-4">
                <div class="card-header">My Courses</div>
                <div class="card-body">
                    <?php if (empty($roleData['myCourses'])): ?>
                        <p class="text-muted">No courses found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Teacher: My Students -->
            <div class="card mb-4">
                <div class="card-header">My Students</div>
                <div class="card-body">
                    <p class="text-muted">No students enrolled yet.</p>
                </div>
            </div>

        <?php elseif ($role === 'student'): ?>
            <!-- Student: My Courses -->
            <div class="card mb-4">
                <div class="card-header">My Courses</div>
                <div class="card-body">
                    <?php if (empty($roleData['enrolledCourses'])): ?>
                        <p class="text-muted">You are not enrolled in any courses yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Student: My Profile -->
            <div class="card mb-4">
                <div class="card-header">My Profile</div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?= esc($user['name']) ?></p>
                    <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
                    <p><strong>Role:</strong> <?= ucfirst(esc($user['role'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
