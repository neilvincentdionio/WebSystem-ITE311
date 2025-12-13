<?= $this->include('templates/header') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Course Materials</h1>
        <div>
            <span class="text-muted">Welcome, <?= esc($student_name) ?></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('student/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Materials</li>
        </ol>
    </nav>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                Available Course Materials
            </h5>
        </div>
        <div class="card-body">
            <?php 
            $allMaterials = $materials;
            $totalMaterials = count($materials['prelim']) + count($materials['midterm']) + count($materials['final']) + count($materials['others']);
            ?>
            
            <?php if ($totalMaterials > 0): ?>
                <!-- Simple Card Layout -->
                <div class="row">
                    <?php foreach (['prelim' => 'Prelim', 'midterm' => 'Midterm', 'final' => 'Final', 'others' => 'Other'] as $key => $label): ?>
                        <?php if (!empty($allMaterials[$key])): ?>
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <?= esc($label) ?> Materials
                                            <span class="badge bg-secondary ms-2"><?= count($allMaterials[$key]) ?></span>
                                        </h5>
                                        <div class="row">
                                            <?php 
                                            $materials = $allMaterials[$key]; 
                                            $category = $label;
                                            include(APPPATH . 'Views/student/materials_category.php'); 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Materials Summary -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Materials Summary</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Total Materials:</strong> <?= $totalMaterials ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Prelim:</strong> <?= count($allMaterials['prelim']) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Midterm:</strong> <?= count($allMaterials['midterm']) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Final:</strong> <?= count($allMaterials['final']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Materials Available</h5>
                    <p class="text-muted">No materials have been uploaded for your approved courses yet.</p>
                    <div class="mt-3">
                        <a href="<?= base_url('student/my-courses') ?>" class="btn btn-primary me-2">
                            <i class="bi bi-book me-2"></i> View My Courses
                        </a>
                        <a href="<?= base_url('student/dashboard') ?>" class="btn btn-secondary">
                            <i class="bi bi-house me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
