<?= $this->include('templates/header') ?>
<div class="container-fluid mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Teacher Schedules</h2>
        <input type="text" class="form-control" placeholder="Search schedules..." id="searchInput" style="width: 250px;">
    </div>
    
    <!-- Schedule Setup Form - Show when teacher is selected from assign_teacher -->
    <?php if (session()->get('setup_course_id') && session()->get('setup_teacher_id')): ?>
        <?php 
            $setupCourseId = session()->get('setup_course_id');
            $setupTeacherId = session()->get('setup_teacher_id');
            $db = \Config\Database::connect();
            $setupCourse = $db->table('courses')->where('id', $setupCourseId)->get()->getRowArray();
            $setupTeacher = $db->table('users')->where('id', $setupTeacherId)->get()->getRowArray();
        ?>
        
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white">Setup Schedule for New Assignment</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-primary">Course Information</h6>
                        <p class="text-muted">
                            <strong><?= esc($setupCourse['title']) ?></strong><br>
                            <?= esc($setupCourse['course_code']) ?> | 
                            <?= esc($setupCourse['department']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Selected Teacher</h6>
                        <p class="text-muted">
                            <strong><?= esc($setupTeacher['name']) ?></strong><br>
                            <?= esc($setupTeacher['email']) ?>
                        </p>
                    </div>
                </div>

                <form action="<?= base_url('admin/schedules/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="course_id" value="<?= esc($setupCourseId) ?>">
                    <input type="hidden" name="teacher_id" value="<?= esc($setupTeacherId) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setup_start_day" class="form-label">Start Day *</label>
                                <select class="form-select" id="setup_start_day" name="start_day" required>
                                    <option value="">Select Start Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setup_end_day" class="form-label">End Day *</label>
                                <select class="form-select" id="setup_end_day" name="end_day" required>
                                    <option value="">Select End Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setup_semester" class="form-label">Semester *</label>
                                <select class="form-select" id="setup_semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="First Semester">First Semester</option>
                                    <option value="Second Semester">Second Semester</option>
                                    <option value="Summer">Summer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setup_term" class="form-label">Term *</label>
                                <select class="form-select" id="setup_term" name="term" required>
                                    <option value="">Select Term</option>
                                    <option value="Term 1">Term 1</option>
                                    <option value="Term 2">Term 2</option>
                                    <option value="Term 3">Term 3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="setup_start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control" id="setup_start_time" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="setup_end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control" id="setup_end_time" name="end_time" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="setup_room_number" class="form-label">Room Number *</label>
                                <select class="form-select" id="setup_room_number" name="room_number" required>
                                    <option value="">Select Room</option>
                                    <option value="101">Room 101</option>
                                    <option value="102">Room 102</option>
                                    <option value="103">Room 103</option>
                                    <option value="104">Room 104</option>
                                    <option value="105">Room 105</option>
                                    <option value="201">Room 201</option>
                                    <option value="202">Room 202</option>
                                    <option value="203">Room 203</option>
                                    <option value="204">Room 204</option>
                                    <option value="205">Room 205</option>
                                    <option value="301">Room 301</option>
                                    <option value="302">Room 302</option>
                                    <option value="303">Room 303</option>
                                    <option value="304">Room 304</option>
                                    <option value="305">Room 305</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setup_building" class="form-label">Building *</label>
                                <select class="form-select" id="setup_building" name="building" required>
                                    <option value="">Select Building</option>
                                    <option value="Main Building">Main Building</option>
                                    <option value="Science Building">Science Building</option>
                                    <option value="Engineering Building">Engineering Building</option>
                                    <option value="Library Building">Library Building</option>
                                    <option value="Administration Building">Administration Building</option>
                                    <option value="Computer Lab Building">Computer Lab Building</option>
                                    <option value="Gymnasium">Gymnasium</option>
                                    <option value="Auditorium">Auditorium</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Next Step:</strong> After configuring schedule, this assignment will be sent to <?= esc($setupTeacher['name']) ?> for approval. The teacher will receive a notification to accept or reject this assignment with the specified schedule details.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('admin/schedules/cancel') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit for Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
    
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
            <?php if (empty($schedules ?? [])): ?>
                <div class="alert alert-info m-3">No schedules found. Teachers must be assigned to courses first.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="schedulesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Teacher</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Term</th>
                                <th>Day Range</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($schedule['teacher_name']) ?></strong>
                                        <br><small class="text-muted"><?= esc($schedule['teacher_email']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($schedule['course_title']) ?></strong>
                                        <br><small class="text-muted"><?= esc($schedule['course_code']) ?></small>
                                    </td>
                                    <td><?= esc($schedule['semester'] ?? '-') ?></td>
                                    <td><?= esc($schedule['term'] ?? '-') ?></td>
                                    <td><?= esc($schedule['day_range'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($schedule['start_time']) && !empty($schedule['end_time'])): ?>
                                            <?= date('h:i A', strtotime($schedule['start_time'])) ?> - <?= date('h:i A', strtotime($schedule['end_time'])) ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($schedule['room_number'])): ?>
                                            <?= esc($schedule['room_number']) ?> (<?= esc($schedule['building'] ?? '-') ?>)
                                            <br><small class="text-muted">Cap: <?= esc($schedule['room_capacity'] ?? '-') ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $schedule['schedule_status'] ?? 'active';
                                            $badgeClass = $status === 'active' ? 'bg-success' : ($status === 'inactive' ? 'bg-danger' : 'bg-warning');
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst(esc($status)) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#scheduleModal"
                                                    onclick="editSchedule(<?= $schedule['course_id'] ?>, '<?= esc($schedule['teacher_name']) ?>', '<?= esc($schedule['course_title']) ?>', '<?= esc($schedule['schedule'] ?? '') ?>')"
                                                    title="Edit Schedule">
                                                <i class="fas fa-clock"></i> Edit Schedule 
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeleteSchedule(<?= $schedule['course_id'] ?>, '<?= esc($schedule['teacher_name']) ?>', '<?= esc($schedule['course_title']) ?>')"
                                                    title="Delete Schedule">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
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

<!-- Schedule Edit Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" size="lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/schedules/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" id="course_id" name="course_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Teacher Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <input type="text" class="form-control" id="teacher_name_display" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" class="form-control" id="course_title_display" readonly>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_day" class="form-label">Start Day *</label>
                                <select class="form-select" id="start_day" name="start_day" required>
                                    <option value="">Select Start Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_day" class="form-label">End Day *</label>
                                <select class="form-select" id="end_day" name="end_day" required>
                                    <option value="">Select End Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="day_range" class="form-label">Day Range (Auto-generated)</label>
                                <input type="text" class="form-control" id="day_range" name="day_range" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number *</label>
                                <select class="form-select" id="room_number" name="room_number" required>
                                    <option value="">Select Room</option>
                                    <option value="101">Room 101</option>
                                    <option value="102">Room 102</option>
                                    <option value="103">Room 103</option>
                                    <option value="104">Room 104</option>
                                    <option value="105">Room 105</option>
                                    <option value="201">Room 201</option>
                                    <option value="202">Room 202</option>
                                    <option value="203">Room 203</option>
                                    <option value="204">Room 204</option>
                                    <option value="205">Room 205</option>
                                    <option value="301">Room 301</option>
                                    <option value="302">Room 302</option>
                                    <option value="303">Room 303</option>
                                    <option value="304">Room 304</option>
                                    <option value="305">Room 305</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="building" class="form-label">Building *</label>
                                <select class="form-select" id="building" name="building" required>
                                    <option value="">Select Building</option>
                                    <option value="Main Building">Main Building</option>
                                    <option value="Science Building">Science Building</option>
                                    <option value="Engineering Building">Engineering Building</option>
                                    <option value="Library Building">Library Building</option>
                                    <option value="Administration Building">Administration Building</option>
                                    <option value="Computer Lab Building">Computer Lab Building</option>
                                    <option value="Gymnasium">Gymnasium</option>
                                    <option value="Auditorium">Auditorium</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="room_capacity" class="form-label">Room Capacity *</label>
                                <input type="number" class="form-control" id="room_capacity" name="room_capacity" placeholder="e.g., 50" required>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> The system will check for schedule conflicts with other courses assigned to this teacher on the same day and time.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the schedule for <strong id="deleteCourseTitle"></strong> assigned to <strong id="deleteTeacherName"></strong>?</p>
                <p class="text-warning">This action will remove all schedule details (day range, time, room, and status) for this course.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="course_id" id="deleteCourseId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    table = document.getElementById('schedulesTable');
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

// Edit schedule function - populate form with current schedule data
function editSchedule(courseId, teacherName, courseTitle, currentSchedule) {
    document.getElementById('course_id').value = courseId;
    document.getElementById('teacher_name_display').value = teacherName;
    document.getElementById('course_title_display').value = courseTitle;
    
    // Get the table row to extract schedule details
    var button = event.target.closest('button');
    var row = button.closest('tr');
    var cells = row.getElementsByTagName('td');
    
    // Extract values from table cells
    var dayRange = cells[4].textContent.trim();
    var timeText = cells[5].textContent.trim();
    var roomText = cells[6].textContent.trim();
    var statusText = cells[7].textContent.trim();
    
    // Parse day range and set start/end days
    if (dayRange && dayRange !== '-') {
        var days = dayRange.split('-').map(d => d.trim());
        if (days.length === 2) {
            document.getElementById('start_day').value = days[0];
            document.getElementById('end_day').value = days[1];
        } else if (days.length === 1) {
            document.getElementById('start_day').value = days[0];
            document.getElementById('end_day').value = days[0];
        }
        document.getElementById('day_range').value = dayRange;
    } else {
        document.getElementById('start_day').value = '';
        document.getElementById('end_day').value = '';
        document.getElementById('day_range').value = '';
    }
    
    // Parse and set times
    if (timeText && timeText !== 'Not Set') {
        var times = timeText.split(' - ');
        if (times.length === 2) {
            // Convert 12-hour format back to 24-hour format for input
            document.getElementById('start_time').value = convertTo24Hour(times[0].trim());
            document.getElementById('end_time').value = convertTo24Hour(times[1].trim());
        }
    } else {
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value = '';
    }
    
    // Parse and set room details
    if (roomText && roomText !== 'Not Set') {
        var roomMatch = roomText.match(/^(\S+)\s*\(([^)]+)\)/);
        if (roomMatch) {
            document.getElementById('room_number').value = roomMatch[1];
            document.getElementById('building').value = roomMatch[2];
        }
        var capMatch = roomText.match(/Cap:\s*(\d+)/);
        if (capMatch) {
            document.getElementById('room_capacity').value = capMatch[1];
        }
    } else {
        document.getElementById('room_number').value = '';
        document.getElementById('building').value = '';
        document.getElementById('room_capacity').value = '';
    }
}

// Auto-generate day range from start and end day
document.addEventListener('DOMContentLoaded', function() {
    var startDaySelect = document.getElementById('start_day');
    var endDaySelect = document.getElementById('end_day');
    var dayRangeInput = document.getElementById('day_range');
    
    function updateDayRange() {
        var startDay = startDaySelect.value;
        var endDay = endDaySelect.value;
        
        if (startDay && endDay) {
            if (startDay === endDay) {
                dayRangeInput.value = startDay;
            } else {
                dayRangeInput.value = startDay + '-' + endDay;
            }
        } else {
            dayRangeInput.value = '';
        }
    }
    
    if (startDaySelect) {
        startDaySelect.addEventListener('change', updateDayRange);
    }
    if (endDaySelect) {
        endDaySelect.addEventListener('change', updateDayRange);
    }
});

// Helper function to convert 12-hour format to 24-hour format for input
function convertTo24Hour(timeStr) {
    var time = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (!time) return '';
    
    var hours = parseInt(time[1]);
    var minutes = time[2];
    var period = time[3].toUpperCase();
    
    if (period === 'PM' && hours !== 12) {
        hours += 12;
    } else if (period === 'AM' && hours === 12) {
        hours = 0;
    }
    
    return (hours < 10 ? '0' : '') + hours + ':' + minutes;
}

// Delete schedule confirmation function
function confirmDeleteSchedule(courseId, teacherName, courseTitle) {
    document.getElementById('deleteCourseId').value = courseId;
    document.getElementById('deleteTeacherName').textContent = teacherName;
    document.getElementById('deleteCourseTitle').textContent = courseTitle;
    document.getElementById('deleteForm').action = '/admin/schedules/delete/' + courseId;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
