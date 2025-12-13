<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (function_exists('csrf_token')): ?>
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
    <?php endif; ?>
    <title><?= esc($title ?? 'LMS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Side Navigation -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $session = session(); ?>
                    <?php if ($session->get('isLoggedIn')): ?>
                        <!-- Admin links -->
                        <?php if ($session->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/courses') ?>">Manage Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('materials') ?>">Upload Material</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/enrollments') ?>">Enrollment Management</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/schedules') ?>">Manage Schedules</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('users') ?>">User Management</a>
                            </li>
                            

                        <!-- Teacher links -->
                        <?php elseif ($session->get('role') === 'teacher'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/courses') ?>">My Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/schedule') ?>">My Schedule</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('materials') ?>">Upload Material</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/students') ?>">My Students</a>
                            </li>
                

                        <!-- Student links -->
                        <?php elseif ($session->get('role') === 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('student/my-schedule') ?>">My Schedule</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('student/my-courses') ?>">My Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('student/materials') ?>">Materials</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('student/enrollment-requests') ?>">Enrollment Requests</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('courses') ?>">Browse Courses</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Right Side User Info / Auth Links -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if ($session->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="pointer-events: none; font-size: 0.65rem; padding: 0.25em 0.5em; min-width: 18px;">0</span>
                            </a>
                            <div id="notif-menu" class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notifDropdown" style="min-width: 320px; z-index: 2000; margin-top: 0.5rem;">
                                <div class="p-3 text-center text-muted">No notifications</div>
                            </div>
                        </li>
                        <li class="nav-item me-3 d-flex align-items-center">
                            <span class="navbar-text text-white fw-semibold mb-0">
                                <?= esc($session->get('name')) ?> 
                                (<?= ucfirst(esc($session->get('role'))) ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger fw-bold" href="<?= base_url('auth/logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        window.BASE_URL = '<?= rtrim(base_url('/'), '/') ?>/';
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      (function () {
        const BASE_URL = window.BASE_URL || '/';
        const badge = document.getElementById('notif-badge');
        const menu = document.getElementById('notif-menu');
        if (!badge || !menu) return;

        function updateBadge(count) {
          if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('d-none');
          } else {
            badge.textContent = '0';
            badge.classList.add('d-none');
          }
        }

        function renderList(items) {
          if (!Array.isArray(items) || items.length === 0) {
            menu.innerHTML = '<div class="p-3 text-center text-muted">No notifications</div>';
            return;
          }
          const parts = [];
          parts.push('<div class="list-group list-group-flush">');
          items.forEach(function (n) {
            const id = n.id;
            const msg = (n.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const created = n.created_at ? new Date(n.created_at).toLocaleString() : '';
            parts.push(
              '<div class="list-group-item">'
              + '<div class="d-flex justify-content-between align-items-start">'
              + '<div class="me-2">'
              + '<div class="alert alert-info mb-1 py-1 px-2">' + msg + '</div>'
              + (created ? '<small class="text-muted">' + created + '</small>' : '')
              + '</div>'
              + '<button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-action="mark-read" data-id="' + id + '">Mark as read</button>'
              + '</div>'
              + '</div>'
            );
          });
          parts.push('</div>');
          menu.innerHTML = parts.join('');
        }

        function fetchAndRender() {
          $.get(BASE_URL + 'notifications')
            .done(function (data) {
              const count = data && typeof data.count === 'number' ? data.count : 0;
              const items = data && Array.isArray(data.items) ? data.items : [];
              updateBadge(count);
              renderList(items);
            })
            .fail(function () {
              // optional: keep UI as-is
            });
        }

        // Delegate mark-as-read
        $(document).on('click', '[data-action="mark-read"]', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const $btn = $(this);
          const id = $btn.data('id');
          if (!id) return;
          const tokenName = $('meta[name="csrf-token-name"]').attr('content');
          const tokenValue = $('meta[name="csrf-token-value"]').attr('content');
          const payload = {};
          if (tokenName && tokenValue) payload[tokenName] = tokenValue;
          $.post(BASE_URL + 'notifications/mark_read/' + id, payload)
            .done(function (res) {
              if (res && res.success) {
                // Optimistically remove the item and decrement the badge
                const item = $btn.closest('.list-group-item');
                if (item.length) item.remove();
                const current = parseInt(badge.textContent || '0', 10) || 0;
                const next = Math.max(0, current - 1);
                updateBadge(next);
                // Re-fetch to keep list in sync
                fetchAndRender();
              }
            });
        });

        // Ensure dropdown toggles reliably and fetch on open
        $('#notifDropdown').on('click', function (e) {
          e.preventDefault();
          if (window.bootstrap && bootstrap.Dropdown) {
            const inst = bootstrap.Dropdown.getOrCreateInstance(this);
            inst.toggle();
          }
        });
        $('#notifDropdown').on('show.bs.dropdown', fetchAndRender);

        // Initial load and 60s polling
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', fetchAndRender);
        } else {
          fetchAndRender();
        }
        setInterval(fetchAndRender, 60000);
      })();
    </script>