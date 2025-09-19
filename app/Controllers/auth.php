<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        return view('auth/register');
    }

    public function registerPost()
    {
        $validation = \Config\Services::validation();
        $userModel  = new UserModel();

        $rules = [
            'name'         => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]',
            'role'         => 'required|in_list[student,instructor,admin]',
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

    public function login()
    {
        return view('auth/login');
    }

    public function loginPost()
    {
        $session   = session();
        $userModel = new UserModel();

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            $sessionData = [
                'id'        => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'isLoggedIn'=> true,
            ];
            $session->set($sessionData);

            return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        $session = session();

        //  Manual check (no filters needed)
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first.');
        }

        return view('auth/dashboard', [
            'title' => 'User Dashboard',
            'user'  => $session->get(), // pass all session data
        ]);
    }
}
