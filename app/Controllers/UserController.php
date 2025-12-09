<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    protected $userModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Display a listing of active users
     */
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can manage users.');
            return redirect()->to('/dashboard');
        }

        $data = [
            'users' => $this->userModel->getActiveUsers(),
            'title' => 'User Management',
            'loggedInUserId' => $session->get('id') // Pass logged-in user ID to view
        ];

        return view('users/index', $data);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can create users.');
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Create New User',
            'validation' => $this->validation
        ];

        return view('users/create', $data);
    }

    /**
     * Store a newly created user in storage
     */
    public function store()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can create users.');
            return redirect()->to('/dashboard');
        }

        // Validation rules
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Name is required.',
                    'min_length' => 'Name must be at least 3 characters.',
                    'max_length' => 'Name cannot exceed 100 characters.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique' => 'This email is already registered.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'Password must be at least 6 characters.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[admin,teacher,student]',
                'errors' => [
                    'required' => 'Role is required.',
                    'in_list' => 'Please select a valid role.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Hash password
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $password,
            'role' => $this->request->getPost('role')
        ];

        // Insert user
        if ($this->userModel->insert($data)) {
            $session->setFlashdata('success', 'User created successfully.');
            return redirect()->to('/users');
        } else {
            $session->setFlashdata('error', 'Failed to create user. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can edit users.');
            return redirect()->to('/dashboard');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/users');
        }

        // RULE 2: Prevent admin from editing their own account
        $loggedInUserId = $session->get('id');
        if ($loggedInUserId == $id) {
            $session->setFlashdata('error', 'You cannot edit your own account.');
            return redirect()->to('/users');
        }

        $data = [
            'user' => $user,
            'title' => 'Edit User',
            'validation' => $this->validation
        ];

        return view('users/edit', $data);
    }

    /**
     * Update the specified user in storage
     */
    public function update($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can update users.');
            return redirect()->to('/dashboard');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/users');
        }

        // RULE 2: Prevent admin from updating their own account
        $loggedInUserId = $session->get('id');
        if ($loggedInUserId == $id) {
            $session->setFlashdata('error', 'You cannot update your own account.');
            return redirect()->to('/users');
        }

        // Validation rules
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Name is required.',
                    'min_length' => 'Name must be at least 3 characters.',
                    'max_length' => 'Name cannot exceed 100 characters.'
                ]
            ],
            'email' => [
                'rules' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'errors' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique' => 'This email is already registered.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[admin,teacher,student]',
                'errors' => [
                    'required' => 'Role is required.',
                    'in_list' => 'Please select a valid role.'
                ]
            ]
        ];

        // Password is optional on update
        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'rules' => 'min_length[6]',
                'errors' => [
                    'min_length' => 'Password must be at least 6 characters.'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role')
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Update user
        if ($this->userModel->update($id, $data)) {
            $session->setFlashdata('success', 'User updated successfully.');
            return redirect()->to('/users');
        } else {
            $session->setFlashdata('error', 'Failed to update user. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Soft delete the specified user
     */
    public function delete($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can delete users.');
            return redirect()->to('/dashboard');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/users');
        }

        // RULE 1: Prevent deletion of admin users
        if (strtolower($user['role']) === 'admin') {
            $session->setFlashdata('error', 'Admin users cannot be deleted.');
            return redirect()->to('/users');
        }

        // Soft delete user
        if ($this->userModel->delete($id)) {
            $session->setFlashdata('success', 'User deleted successfully.');
        } else {
            $session->setFlashdata('error', 'Failed to delete user. Please try again.');
        }

        return redirect()->to('/users');
    }

    /**
     * Display deleted users (Trash view)
     */
    public function trash()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can view deleted users.');
            return redirect()->to('/dashboard');
        }

        $data = [
            'users' => $this->userModel->getDeletedUsers(),
            'title' => 'Deleted Users (Trash)'
        ];

        return view('users/trash', $data);
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: You must login first.');
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Only administrators can restore users.');
            return redirect()->to('/dashboard');
        }

        // Check if user exists in deleted records
        $deletedUser = $this->userModel->onlyDeleted()->find($id);

        if (!$deletedUser) {
            $session->setFlashdata('error', 'Deleted user not found.');
            return redirect()->to('/users/trash');
        }

        // Restore user
        if ($this->userModel->restoreUser($id)) {
            $session->setFlashdata('success', 'User restored successfully.');
            return redirect()->to('/users');
        } else {
            $session->setFlashdata('error', 'Failed to restore user. Please try again.');
            return redirect()->to('/users/trash');
        }
    }
}

