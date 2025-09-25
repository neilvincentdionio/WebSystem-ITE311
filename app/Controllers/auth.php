<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $validation = \Config\Services::validation();
            $userModel  = new UserModel();

            $rules = [
                'name'         => 'required|min_length[3]',
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
                    'id'        => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'isLoggedIn'=> true,
                ]);

                // Redirect all users to the same dashboard
                return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
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

        // Block access if not logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($session->get('id')); // Fetch fresh user data
        $role      = $user['role'];

        // Default roleData
        $roleData = [
            'message' => '',
            'users'   => [],
            'myCourses' => [],
            'enrolledCourses' => [],
        ];

        // Role-specific data
        if ($role === 'admin') {
            $roleData['message'] = 'You have full administrator access.';
            $roleData['users']   = $userModel->findAll(); // only DB-backed data
        } elseif ($role === 'teacher') {
            $roleData['message']   = 'You can manage your classes and students here.';
            $roleData['myCourses'] = []; // no DB yet
        } elseif ($role === 'student') {
            $roleData['message']          = 'You can view your enrolled courses and progress.';
            $roleData['enrolledCourses']  = []; // no DB yet
        }

        // Load the unified dashboard view
        return view('auth/dashboard', [
            'title'    => 'User Dashboard',
            'user'     => $user,
            'role'     => $role,
            'roleData' => $roleData,
        ]);
    }
}
