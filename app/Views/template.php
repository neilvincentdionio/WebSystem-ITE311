<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My CI4 System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">My CI4 System</a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('about') ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('contact') ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a>
                </li>
            </ul>

            <!-- Auth Buttons (right side) -->
            <div class="d-flex">
                <?php if (session()->get('logged_in')): ?>
                    <span class="navbar-text text-white me-3">
                        <?= esc(session()->get('name')) ?> (<?= esc(session()->get('role')) ?>)
                    </span>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-light me-2">Dashboard</a>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('auth/register') ?>" class="btn btn-outline-light me-2">Register</a>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-primary">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container mt-5">
    <?= $this->renderSection('content') ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
