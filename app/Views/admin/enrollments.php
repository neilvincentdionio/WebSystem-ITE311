<?= $this->include('templates/header') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Enrollment Management</h2>
        <input type="text" class="form-control" placeholder="Search courses..." id="searchInput" style="width: 250px;">
    </div>
    
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
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($courses ?? [])): ?>
                <div class="alert alert-info m-3">No courses found. Click "Add Course" to add your first course.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="coursesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Teacher</th>
                                <th>Department</th>
                                <th>Program</th>
                                <th>Schedule</th>
                                <th>Enrolled Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td><?= esc($c['course_code'] ?? '-') ?></td>
                                    <td>
                                        <strong><?= esc($c['title']) ?></strong>
                                        <?php if (!empty($c['description'])): ?>
                                            <br><small class="text-muted"><?= esc(substr($c['description'], 0, 80)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($c['teacher_name'] ?? 'Not Assigned') ?></td>
                                    <td><?= esc($c['department'] ?? '-') ?></td>
                                    <td><?= esc($c['program'] ?? '-') ?></td>
                                    <td>
                                        <?php if (isset($c['schedule_info'])): ?>
                                            <small class="<?= $c['schedule_info'] === 'No schedule set' ? 'text-muted' : 'text-success' ?>">
                                                <?= esc($c['schedule_info']) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">No schedule set</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $c['enrollment_count'] ?? 0 ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-sm btn-outline-info" href="<?= base_url('admin/enrollments/view/' . $c['id']) ?>" title="View Enrollments">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/enrollments/course/' . $c['id']) ?>" title="Enroll Student">
                                                <i class="fas fa-user-plus"></i> Enroll Student
                                            </a>
                                            <?php if (strtolower(session()->get('role')) === 'admin'): ?>
                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/courses/assign-teacher/' . $c['id']) ?>" title="Assign Teacher">
                                                    <i class="fas fa-chalkboard-teacher"></i> Assign Teacher
                                                </a>
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

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    table = document.getElementById('coursesTable');
    tr = table.getElementsByTagName('tr');
    
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = 'none';
        td = tr[i].getElementsByTagName('td');
        for (var j = 0; j < td.length; j++) {
            txtValue = td[j].textContent || td[j].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
                break;
            }
        }
    }
});
</script>

