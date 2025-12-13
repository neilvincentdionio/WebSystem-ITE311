<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $materials;
    protected $enrollments;
    protected $db;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->materials   = new MaterialModel();
        $this->enrollments = new EnrollmentModel();
        $this->db          = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // Admin/Teacher: Materials dashboard/index
    public function index()
    {
        $session = session();
        $isLoggedIn = (bool) $session->get('isLoggedIn');
        $role = strtolower((string) $session->get('role'));
        
        if (!$isLoggedIn) {
            return redirect()->to(base_url('auth/login'));
        }
        
        if (!in_array($role, ['admin', 'teacher'])) {
            return redirect()->to(base_url('announcements'))->with('error', 'Access Denied: Insufficient Permissions.');
        }

        // Get all courses for admin/teacher to manage materials
        $courses = [];
        try {
            $hasCourses = $this->db->query("SHOW TABLES LIKE 'courses'")->getNumRows() > 0;
            if ($hasCourses) {
                $builder = $this->db->table('courses');
                if ($role === 'teacher') {
                    // Teachers can only see their assigned courses
                    $builder->where('teacher_id', (int)$session->get('id'));
                }
                $courses = $builder->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('error', 'Materials index error: {err}', ['err' => $e->getMessage()]);
        }

        return view('materials/materials_index', [
            'courses' => $courses,
            'role' => $role
        ]);
    }

    // Admin/Instructor: Upload material for a course (GET form, POST upload)
    public function upload($course_id)
    {
        $session = session();
        // Map to your existing session keys
        $isLoggedIn = (bool) $session->get('isLoggedIn');
        $role       = strtolower((string) $session->get('role'));
        if (! $isLoggedIn) {
            return redirect()->to(base_url('auth/login'));
        }
        if (! in_array($role, ['admin','teacher'])) {
            return redirect()->to(base_url('announcements'))->with('error', 'Access Denied: Insufficient Permissions.');
        }

        // Validate course exists (handle table missing gracefully)
        $courseRow = null;
        try {
            $hasCourses = $this->db->query("SHOW TABLES LIKE 'courses'")->getNumRows() > 0;
            if ($hasCourses) {
                $courseRow = $this->db->table('courses')->where('id', (int) $course_id)->get()->getRowArray();
            }
        } catch (\Throwable $e) {
            // ignore, courseRow remains null
        }
        if (! $courseRow) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Invalid course.');
        }

        $materialModel = new MaterialModel();

        // Detect POST via server var for reliability
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');
        log_message('info', 'Materials::upload isPost={post}', ['post' => $isPost ? 'YES' : 'NO']);

        if ($isPost) {
            try {
                log_message('info', 'Materials upload POST received for course_id={id}', ['id' => (int)$course_id]);

                $rules = [
                    'exam_type' => [
                        'label' => 'Exam Type',
                        'rules' => 'required|in_list[prelim,midterm,finals]'
                    ],
                    'file' => [
                        'label' => 'Material File',
                        'rules' => 'uploaded[file]|max_size[file,51200]|ext_in[file,pdf,ppt,pptx,doc,docx,xls,xlsx,csv,zip,rar,7z,txt,jpg,jpeg,png]'
                    ]
                ];

                if (! $this->validate($rules)) {
                    $errs = $this->validator ? $this->validator->getErrors() : [];
                    $msg  = ! empty($errs) ? implode("\n", $errs) : 'Validation failed.';
                    log_message('error', 'Materials upload validation failed: {msg}', ['msg' => $msg]);
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => $msg,
                    ]);
                }

                $file = $this->request->getFile('file');
                if (! $file) {
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => 'No file received. Check php.ini post_max_size and upload_max_filesize.',
                    ]);
                }

                if ($file->getError() !== UPLOAD_ERR_OK) {
                    $errMsg = $file->getErrorString() ?: ('Upload error code: ' . $file->getError());
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => 'Upload failed: ' . $errMsg,
                    ]);
                }

                $uploadBase = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads';
                $materialsRoot = $uploadBase . DIRECTORY_SEPARATOR . 'materials';
                $courseFolderName = 'Course ' . (int)$course_id;
                $uploadDir  = $materialsRoot . DIRECTORY_SEPARATOR . $courseFolderName; // per-course subfolder
                if (! is_dir($uploadDir)) {
                    if (! @mkdir($uploadDir, 0777, true) && ! is_dir($uploadDir)) {
                        $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                        return view('materials/materials_upload', [
                            'course'     => $courseRow,
                            'materials'  => $materials,
                            'error'      => 'Cannot create upload directory.',
                        ]);
                    }
                }
                if (! is_writable($uploadDir)) {
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => 'Upload directory is not writable: ' . $uploadDir,
                    ]);
                }

                // Use original client filename, sanitized, and ensure uniqueness
                $origName = $file->getClientName();
                $origName = trim(str_replace(['\\', '/'], '', (string) $origName));
                if ($origName === '') {
                    $origName = 'upload_' . date('Ymd_His');
                }
                // sanitize: allow letters, numbers, spaces, dots, dashes, underscores
                $sanitized = preg_replace('/[^A-Za-z0-9 ._\-]+/', '_', $origName);
                $finalName = $sanitized;
                $counter = 1;
                $pi = pathinfo($sanitized);
                $base = $pi['filename'] ?? 'file';
                $ext  = isset($pi['extension']) && $pi['extension'] !== '' ? ('.' . $pi['extension']) : '';
                while (is_file($uploadDir . DIRECTORY_SEPARATOR . $finalName)) {
                    $finalName = $base . ' (' . $counter . ')' . $ext;
                    $counter++;
                }

                try {
                    if (! $file->move($uploadDir, $finalName)) {
                        $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                        return view('materials/materials_upload', [
                            'course'     => $courseRow,
                            'materials'  => $materials,
                            'error'      => 'Failed to move uploaded file.',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $err = method_exists($file, 'getErrorString') ? $file->getErrorString() : '';
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => 'File move failed: ' . $e->getMessage() . ($err ? (' | ' . $err) : ''),
                    ]);
                }

                $data = [
                    'course_id'  => (int) $course_id,
                    'exam_type'  => $this->request->getPost('exam_type'),
                    'file_name'  => $file->getClientName(),
                    'file_path'  => 'uploads/materials/' . $courseFolderName . '/' . $finalName,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                log_message('info', 'Attempting to insert material: {data}', ['data' => json_encode($data)]);

                try {
                    $builder  = $this->db->table('materials');
                    $ok       = $builder->insert($data);
                    $insertID = $this->db->insertID();
                    $affected = $this->db->affectedRows();
                    $dbErr    = $this->db->error();

                    log_message('info', 'DB insert - ok={ok}, id={id}, affected={aff}, err={err}', [
                        'ok'  => $ok ? 'true' : 'false',
                        'id'  => $insertID,
                        'aff' => $affected,
                        'err' => json_encode($dbErr),
                    ]);

                    if (! $ok || $affected < 1) {
                        $msg = 'Failed to save material record. Affected: ' . $affected;
                        if (! empty($dbErr['message'])) {
                            $msg .= ' DB: ' . $dbErr['message'];
                        }
                        log_message('error', 'Materials insert failed: {msg}', ['msg' => $msg]);
                        $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                        return view('materials/materials_upload', [
                            'course'     => $courseRow,
                            'materials'  => $materials,
                            'error'      => $msg,
                        ]);
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Materials upload DB error: {err}', ['err' => $e->getMessage()]);
                    $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                    return view('materials/materials_upload', [
                        'course'     => $courseRow,
                        'materials'  => $materials,
                        'error'      => 'Database error: ' . $e->getMessage(),
                    ]);
                }

                log_message('info', 'Material upload completed successfully');
                $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                return view('materials/materials_upload', [
                    'course'     => $courseRow,
                    'materials'  => $materials,
                    'success'    => 'Material uploaded successfully.',
                ]);
            } catch (\Throwable $uploadEx) {
                log_message('error', 'FATAL upload error: {err}', ['err' => $uploadEx->getMessage()]);
                $materials = $materialModel->getMaterialsByCourse((int)$course_id);
                return view('materials/materials_upload', [
                    'course'     => $courseRow,
                    'materials'  => $materials,
                    'error'      => 'FATAL ERROR: ' . $uploadEx->getMessage(),
                ]);
            }
        }

        // GET
        $materials = $materialModel->getMaterialsByCourse((int)$course_id);
        return view('materials/materials_upload', [
            'course'    => $courseRow,
            'materials' => $materials,
        ]);
    }

    // Admin/Instructor: Delete material and its file
    public function delete($material_id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }
        if (strtolower($session->get('role')) !== 'admin' && strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions.');
        }

        $material = $this->materials->getById((int)$material_id);
        if (! $material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Remove file
        $filePath = WRITEPATH . $material['file_path'];
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $this->materials->deleteById((int)$material_id);
        return redirect()->back()->with('success', 'Material deleted.');
    }

    // Students: Download material if enrolled in the course (admins/teachers also allowed)
    public function download($material_id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        $material = $this->materials->getById((int)$material_id);
        if (! $material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        $role = strtolower((string)$session->get('role'));
        $userId = (int)$session->get('id');

        $allowed = in_array($role, ['admin', 'teacher']);
        if (! $allowed) {
            // check enrollment
            log_message('debug', 'Materials download: Student ID: ' . $userId . ', Course ID: ' . (int)$material['course_id']);
            $enrollmentCheck = $this->db->table('student_enrollments')
                ->where('student_id', $userId)
                ->where('course_id', (int)$material['course_id'])
                ->where('status', 'approved')
                ->get()
                ->getRowArray();
            $allowed = !empty($enrollmentCheck);
            log_message('debug', 'Materials download: Enrollment check result: ' . ($allowed ? 'ALLOWED' : 'DENIED'));
        }

        if (! $allowed) {
            return redirect()->back()->with('error', 'Access denied. You are not enrolled in this course.');
        }

        $filePath = WRITEPATH . $material['file_path'];
        if (! is_file($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($material['file_name']);
    }

    // Optional: Public listing for students for a specific course
    public function listing($course_id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        // Verify enrollment or admin/teacher
        $role   = strtolower((string)$session->get('role'));
        $userId = (int)$session->get('id');

        $course = $this->db->table('courses')->where('id', (int)$course_id)->get()->getRowArray();
        if (! $course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        $allowed = in_array($role, ['admin', 'teacher']) || $this->enrollments->isAlreadyEnrolled($userId, (int)$course_id);
        if (! $allowed) {
            return redirect()->to('/announcements')->with('error', 'Access denied. You are not enrolled in this course.');
        }

        $materials = $this->materials->getMaterialsByCourse((int)$course_id);
        return view('materials/materials_list', [
            'course'    => $course,
            'materials' => $materials,
        ]);
    }
}
