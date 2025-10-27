<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'LMS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Side Navigation -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $session = session(); ?>
                    <?php if ($session->get('isLoggedIn')): ?>
                        <li class="nav-item">
                            <?php $role = strtolower((string)$session->get('role')); $dashUrl = ($role === 'admin') ? 'admin/dashboard' : 'dashboard'; ?>
                            <a class="nav-link" href="<?= base_url($dashUrl) ?>">Dashboard</a>
                        </li>

                        <!-- Admin links -->
                        <?php if ($session->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/courses') ?>">Manage Courses</a>
                            </li>
                            

                        <!-- Teacher links -->
                        <?php elseif ($session->get('role') === 'teacher'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('my_courses') ?>">My Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('my_students') ?>">My Students</a>
                            </li>

                        <!-- Student links -->
                        <?php elseif ($session->get('role') === 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a>
                            </li>   
                            

                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Right Side User Info / Auth Links -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if ($session->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="bi bi-bell" aria-hidden="true"></span>
                                <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                            </a>
                            <div id="notif-menu" class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notifDropdown" style="min-width: 320px;">
                                <div class="p-3 text-center text-muted">No notifications</div>
                            </div>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <span class="navbar-text text-white me-3 fw-semibold">
                                <?= esc($session->get('name')) ?> 
                                (<?= ucfirst(esc($session->get('role'))) ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger fw-bold" href="<?= base_url('auth/logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    