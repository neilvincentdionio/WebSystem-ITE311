<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 🚨 Check if user is NOT logged in
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'You must login first.');
        }

        // ✅ If specific roles are required
        if ($arguments) {
            $userRole = session()->get('role');
            if (! in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Unauthorized access.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
