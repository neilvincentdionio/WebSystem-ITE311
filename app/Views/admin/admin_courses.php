 <?= $this->include('templates/header') ?>
<div class="container mt-5">
    <h2>Welcome, Admin!</h2>
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-dark text-white">Courses</div>
        <div class="card-body">
            <?php if (empty($courses ?? [])): ?>
                <div class="alert alert-info mb-0">No courses found. Run CourseSeeder or add courses.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Title</th>
                                <th style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td><?= esc($c['id']) ?></td>
                                    <td><?= esc($c['title']) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="<?= base_url('course/' . $c['id'] . '/upload') ?>">Upload</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('course/' . $c['id'] . '/materials') ?>">View Materials</a>
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
