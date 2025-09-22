<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        // Handle POST request (registration submission)
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

        // Handle GET request (show registration form)
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
            // Regenerate session to prevent fixation attacks
            $session->regenerate();

            // Set session data
            $sessionData = [
                'id'        => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'isLoggedIn'=> true,
            ];
            $session->set($sessionData);

            switch ($user['role']) {
                case UserModel::ROLE_ADMIN:
                    $redirectUrl = '/admin/dashboard';
                    break;

                case UserModel::ROLE_TEACHER:
                    $redirectUrl = '/teacher/dashboard';
                    break;

                case UserModel::ROLE_STUDENT:
                    $redirectUrl = '/student/dashboard';
                    break;

                default:
                    $redirectUrl = '/dashboard'; // fallback
            }

            return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user['name'] . '!');
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

        return view('auth/dashboard', [
            'title' => 'User Dashboard',
            'user'  => $session->get(), // pass all session data
        ]);
    }
}