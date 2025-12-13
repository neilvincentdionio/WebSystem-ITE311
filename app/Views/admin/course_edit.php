<?= $this->include('templates/header') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Course</h2>
        <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> Please fix the following issues:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Edit Course Information</div>
        <div class="card-body">
            <form action="<?= base_url('admin/courses/update/' . $course['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title', $course['title']) ?>" required maxlength="150">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" 
                                   value="<?= old('course_code', $course['course_code']) ?>" maxlength="50" placeholder="e.g., CS301">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              maxlength="1000"><?= old('description', $course['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Select Department</option>
                                <?php if (isset($departments) && is_array($departments)): ?>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= esc($dept['department']) ?>" 
                                                <?= old('department', $course['department']) == $dept['department'] ? 'selected' : '' ?>>
                                            <?= esc($dept['department']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <option value="Department of Engineering and Technology" <?= old('department', $course['department']) == 'Department of Engineering and Technology' ? 'selected' : '' ?>>Department of Engineering and Technology</option>
                                <option value="Department of Arts" <?= old('department', $course['department']) == 'Department of Arts' ? 'selected' : '' ?>>Department of Arts</option>
                                <option value="Department of Business" <?= old('department', $course['department']) == 'Department of Business' ? 'selected' : '' ?>>Department of Business</option>
                                <option value="Department of Science" <?= old('department', $course['department']) == 'Department of Science' ? 'selected' : '' ?>>Department of Science</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="program" class="form-label">Program</label>
                            <select class="form-select" id="program" name="program">
                                <option value="">Select Program</option>
                                <?php if (isset($programs) && is_array($programs)): ?>
                                    <?php foreach ($programs as $prog): ?>
                                        <option value="<?= esc($prog['program']) ?>" 
                                                data-department="<?= esc($prog['department'] ?? '') ?>"
                                                <?= old('program', $course['program']) == $prog['program'] ? 'selected' : '' ?>>
                                            <?= esc($prog['program']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Engineering and Technology Programs -->
                                <option value="Bachelor of Science in Computer Science" data-department="Department of Engineering and Technology" <?= old('program', $course['program']) == 'Bachelor of Science in Computer Science' ? 'selected' : '' ?>>Bachelor of Science in Computer Science</option>
                                <option value="Bachelor of Science in Information Technology" data-department="Department of Engineering and Technology" <?= old('program', $course['program']) == 'Bachelor of Science in Information Technology' ? 'selected' : '' ?>>Bachelor of Science in Information Technology</option>
                                <option value="Bachelor of Science in Computer Engineering" data-department="Department of Engineering and Technology" <?= old('program', $course['program']) == 'Bachelor of Science in Computer Engineering' ? 'selected' : '' ?>>Bachelor of Science in Computer Engineering</option>
                                <!-- Business Programs -->
                                <option value="Bachelor of Science in Business Administration" data-department="Department of Business" <?= old('program', $course['program']) == 'Bachelor of Science in Business Administration' ? 'selected' : '' ?>>Bachelor of Science in Business Administration</option>
                                <!-- Arts Programs -->
                                <option value="Bachelor of Arts in Fine Arts" data-department="Department of Arts" <?= old('program', $course['program']) == 'Bachelor of Arts in Fine Arts' ? 'selected' : '' ?>>Bachelor of Arts in Fine Arts</option>
                                <option value="Bachelor of Arts in Literature" data-department="Department of Arts" <?= old('program', $course['program']) == 'Bachelor of Arts in Literature' ? 'selected' : '' ?>>Bachelor of Arts in Literature</option>
                                <!-- Science Programs -->
                                <option value="Bachelor of Science in Biology" data-department="Department of Science" <?= old('program', $course['program']) == 'Bachelor of Science in Biology' ? 'selected' : '' ?>>Bachelor of Science in Biology</option>
                                <option value="Bachelor of Science in Chemistry" data-department="Department of Science" <?= old('program', $course['program']) == 'Bachelor of Science in Chemistry' ? 'selected' : '' ?>>Bachelor of Science in Chemistry</option>
                                <!-- General Education -->
                                <option value="General Education" data-department="Department of Arts" <?= old('program', $course['program']) == 'General Education' ? 'selected' : '' ?>>General Education</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                   value="<?= old('academic_year', $course['academic_year']) ?>" maxlength="20" placeholder="e.g., 2024-2025">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">Select Semester</option>
                                <option value="1st Semester" <?= (old('semester', $course['semester']) == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                                <option value="2nd Semester" <?= (old('semester', $course['semester']) == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                                <option value="Summer" <?= (old('semester', $course['semester']) == 'Summer') ? 'selected' : '' ?>>Summer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="term" class="form-label">Term</label>
                            <select class="form-select" id="term" name="term">
                                <option value="">Select Term</option>
                                <option value="Term 1" <?= (old('term', $course['term']) == 'Term 1') ? 'selected' : '' ?>>Term 1</option>
                                <option value="Term 2" <?= (old('term', $course['term']) == 'Term 2') ? 'selected' : '' ?>>Term 2</option>
                                <option value="Term 3" <?= (old('term', $course['term']) == 'Term 3') ? 'selected' : '' ?>>Term 3</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created</label>
                            <input type="text" class="form-control" value="<?= date('M d, Y h:i A', strtotime($course['created_at'])) ?>" readonly>
                            <div class="form-text">Course creation date</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="updated_at" class="form-label">Last Updated</label>
                            <input type="text" class="form-control" value="<?= date('M d, Y h:i A', strtotime($course['updated_at'])) ?>" readonly>
                            <div class="form-text">Course last update date</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const programSelect = document.getElementById('program');
    const allProgramOptions = Array.from(programSelect.options);
    
    // Filter programs when department changes
    departmentSelect.addEventListener('change', function() {
        const selectedDepartment = this.value;
        
        // Clear program options
        programSelect.innerHTML = '<option value="">Select Program</option>';
        
        if (selectedDepartment) {
            // Add programs that belong to the selected department
            allProgramOptions.forEach(option => {
                if (option.value === '' || option.dataset.department === selectedDepartment) {
                    programSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    });
    
    // Trigger change on page load if department is pre-selected
    if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));
    }
});
</script>

