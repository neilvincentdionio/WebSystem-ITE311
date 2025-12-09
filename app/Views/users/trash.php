<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-trash"></i> Deleted Users (Trash)</h2>
        <a href="<?= base_url('users') ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-archive"></i> Archived Users</h5>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No deleted users found. Trash is empty.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user['id']) ?></td>
                                    <td><?= esc($user['name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $role = esc($user['role']);
                                        $badgeClass = $role === 'admin' ? 'bg-danger' : ($role === 'teacher' ? 'bg-primary' : 'bg-success');
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($role) ?></span>
                                    </td>
                                    <td>
                                        <?= $user['deleted_at'] ? date('M d, Y H:i', strtotime($user['deleted_at'])) : 'N/A' ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('users/restore/' . $user['id']) ?>" 
                                           class="btn btn-sm btn-success" 
                                           title="Restore"
                                           onclick="return confirm('Are you sure you want to restore this user?')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

