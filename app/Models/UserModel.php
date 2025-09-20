<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Fields that are allowed to be inserted/updated
    protected $allowedFields = ['name', 'email', 'password', 'role'];

    // Automatically manage created_at & updated_at fields
    protected $useTimestamps = true;

    protected $returnType    = 'array';

    // Define roles (matching ENUM in migration)
    public const ROLE_ADMIN      = 'admin';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_STUDENT    = 'student';

    /**
     * Check if a user has a specific role
     */
    public function hasRole(int $userId, string $role): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === $role;
    }

    // Shortcuts for each role
    public function isAdmin(int $userId): bool
    {
        return $this->hasRole($userId, self::ROLE_ADMIN);
    }

    public function isInstructor(int $userId): bool
    {
        return $this->hasRole($userId, self::ROLE_INSTRUCTOR);
    }

    public function isStudent(int $userId): bool
    {
        return $this->hasRole($userId, self::ROLE_STUDENT);
    }
}