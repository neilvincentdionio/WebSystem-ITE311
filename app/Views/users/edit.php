<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-gear"></i> Edit User</h2>
        <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> User Information</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('users/update/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <?php $errors = session()->getFlashdata('errors'); ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           id="name" 
                           name="name" 
                           value="<?= old('name', $user['name']) ?>" 
                           required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback">
                            <?= esc($errors['name']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" 
                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" 
                           name="email" 
                           value="<?= old('email', $user['email']) ?>" 
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback">
                            <?= esc($errors['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                           id="password" 
                           name="password">
                    <small class="form-text text-muted">Leave blank to keep current password. Minimum 6 characters if changing.</small>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback">
                            <?= esc($errors['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" 
                            id="role" 
                            name="role" 
                            required>
                        <option value="">Select a role</option>
                        <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="teacher" <?= old('role', $user['role']) === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                        <option value="student" <?= old('role', $user['role']) === 'student' ? 'selected' : '' ?>>Student</option>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <div class="invalid-feedback">
                            <?= esc($errors['role']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

