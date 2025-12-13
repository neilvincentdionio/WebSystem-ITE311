<?php if (!empty($materials)): ?>
    <?php foreach ($materials as $material): ?>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="card-title mb-1"><?= esc($material['file_name']) ?></h6>
                            <small class="text-muted"><?= esc($material['course_code']) ?> - <?= esc($material['course_title']) ?></small>
                        </div>
                        <span class="badge bg-secondary"><?= esc(ucfirst($material['type'] ?? 'Document')) ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <?= date('M j, Y', strtotime($material['created_at'])) ?>
                        </small>
                        <?php if (!empty($material['file_path'])): ?>
                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="text-center py-4">
        <i class="bi bi-folder-open fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No <?= esc($category) ?> Materials Available</h6>
        <p class="text-muted">No materials have been uploaded for the <?= esc(strtolower($category)) ?> period yet.</p>
    </div>
<?php endif; ?>
