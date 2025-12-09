<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> User Management</h2>
        <div>
            <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New User
            </a>
            <a href="<?= base_url('users/trash') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-trash"></i> View Trash
            </a>
        </div>
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
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Active Users</h5>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No users found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Updated At</th>
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
                                    <td><?= $user['created_at'] ? date('M d, Y H:i', strtotime($user['created_at'])) : 'N/A' ?></td>
                                    <td><?= $user['updated_at'] ? date('M d, Y H:i', strtotime($user['updated_at'])) : 'N/A' ?></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <?php 
                                            $loggedInUserId = $loggedInUserId ?? session()->get('id');
                                            $isAdmin = strtolower($user['role']) === 'admin';
                                            $isOwnAccount = ($loggedInUserId == $user['id']);
                                            ?>
                                            
                                            <?php if (!$isOwnAccount): // RULE 2: Hide Edit button for logged-in admin's own account ?>
                                                <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-outline-secondary disabled" title="You cannot edit your own account">
                                                    <i class="bi bi-pencil"></i>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!$isAdmin): // RULE 1: Hide Delete button for admin users ?>
                                                <a href="<?= base_url('users/delete/' . $user['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this user? This action can be undone from the Trash.')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-outline-secondary disabled" title="Admin users cannot be deleted">
                                                    <i class="bi bi-trash"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
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

