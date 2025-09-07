<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center">Register</h2>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('auth/register') ?>">
                    <?= csrf_field() ?>

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="<?= old('name') ?>"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?= old('email') ?>"
                               required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password"
                               name="pass_confirm"
                               class="form-control"
                               required>
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
                            <option value="instructor" <?= old('role') === 'instructor' ? 'selected' : '' ?>>Instructor</option>
                            <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <div class="mt-3 text-center">
                    <p>Already have an account? <a href="<?= base_url('auth/login') ?>">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
