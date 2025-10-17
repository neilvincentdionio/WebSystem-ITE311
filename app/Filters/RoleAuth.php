<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = $session->get('role');

        // If user is not logged in
        if (!$session->get('isLoggedIn') || !$role) {
            $session->setFlashdata('error', 'Access Denied: Please login first.');
            return redirect()->to('/auth/login');
        }

        // Get the current URI
        $uri = service('uri')->getPath();

        // Role-based access control
        switch (strtolower($role)) {
            case 'admin':
                // Admin can access /admin/*
                if (strpos($uri, 'admin') === 0) {
                    return; // allowed
                }
                break;

            case 'teacher':
                // Teacher can access /teacher/*
                if (strpos($uri, 'teacher') === 0) {
                    return; // allowed
                }
                break;

            case 'student':
                // Student can access /student/* and /announcements
                if (strpos($uri, 'student') === 0 || $uri === 'announcements') {
                    return; // allowed
                }
                break;
        }

        // If not allowed, redirect to announcements with flash message
        $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        return redirect()->to('/announcements');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
