<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // user logged in check
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        // Get current role and page path
        $role = $session->get('role'); 
        $uri  = service('uri')->getPath();

        // Determine allowed role from route group
        $allowedRole = $arguments[0] ?? null;

        //  Block access if role doesn’t match
        if ($allowedRole && $role !== $allowedRole) {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions.');
        }

        return $request; // proceed if authorized
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
        return $response;
    }
}