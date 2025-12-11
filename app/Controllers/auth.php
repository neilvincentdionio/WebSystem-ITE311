<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Auth extends BaseController
{
    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $validation = \Config\Services::validation();
            $userModel  = new UserModel();

            $rules = [
                'name'         => 'required|min_length[3]|alpha_space',
                'email'        => 'required|valid_email|is_unique[users.email]',
                'password'     => 'required|min_length[6]',
                'pass_confirm' => 'required|matches[password]',
                'role'         => 'required|in_list[student,teacher,admin]',
            ];

            if (! $this->validate($rules)) {
                return view('auth/register', [
                    'validation' => $validation,
                ]);
            }

            $userModel->save([
                'name'     => $this->request->getPost('name'),
                'email'    => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'     => $this->request->getPost('role'),
            ]);

            return redirect()->to('/auth/login')->with('success', 'Account created successfully! Please login.');
        }

        return view('auth/register');
    }

    public function login()
{
    if ($this->request->getMethod() === 'POST') {
        $session   = session();
        $userModel = new UserModel();

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Prevent session fixation attacks
            $session->regenerate();

            // Set session data
            $session->set([
                'id'         => $user['id'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ]);

            // Redirect based on user role
            switch ($user['role']) {
                case 'student':
                    return redirect()->to('/announcements')
                                     ->with('success', 'Welcome back, ' . $user['name'] . '!');
                case 'teacher':
                    return redirect()->to('/teacher/dashboard')
                                     ->with('success', 'Welcome back, ' . $user['name'] . '!');
                case 'admin':
                    return redirect()->to('/admin/dashboard')
                                     ->with('success', 'Welcome back, ' . $user['name'] . '!');
                default:
                    return redirect()->to('/dashboard')
                                     ->with('success', 'Welcome back, ' . $user['name'] . '!');
            }
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    return view('auth/login');
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out.');
    }

    public function dashboard()
{
    $session = session();

    if (! $session->get('isLoggedIn')) {
        return redirect()->to('/auth/login')->with('error', 'Please login first.');
    }

    $userModel       = new \App\Models\UserModel();
    $enrollmentModel = new \App\Models\EnrollmentModel();
    $db              = \Config\Database::connect();

    $user = $userModel->find($session->get('id'));
    $role = $user['role'];

    // Default roleData
    $roleData = [
        'message'          => '',
        'users'            => [],
        'myCourses'        => [],
        'enrolledCourses'  => [],
        'availableCourses' => [],
    ];

    if ($role === 'admin') {
        $roleData['message'] = 'You have full administrator access.';
        $roleData['users']   = $userModel->findAll();
    } elseif ($role === 'teacher') {
        $roleData['message']   = 'You can manage your classes and students here.';
        $roleData['myCourses'] = []; // Populate later if needed
    } elseif ($role === 'student') {
        $roleData['message'] = 'You can view your enrolled courses and progress.';

        // Fetch enrolled courses
        $roleData['enrolledCourses'] = $enrollmentModel->getUserEnrollments($user['id']);

        // Fetch all courses directly from DB
        $allCourses = $db->table('courses')->get()->getResultArray();

        // Filter out enrolled courses to get available courses
        $enrolledIds = array_column($roleData['enrolledCourses'], 'id');
        $roleData['availableCourses'] = array_filter($allCourses, function($course) use ($enrolledIds) {
            return !in_array($course['id'], $enrolledIds);
        });
    }

    return view('auth/dashboard', [
        'title'    => 'User Dashboard',
        'user'     => $user,
        'role'     => $role,
        'roleData' => $roleData,
    ]);
}

}
