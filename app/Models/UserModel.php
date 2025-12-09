<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Fields that are allowed to be inserted/updated
    protected $allowedFields = ['name', 'email', 'password', 'role', 'deleted_at'];

    // Automatically manage created_at & updated_at fields
    protected $useTimestamps = true;

    // Enable soft deletes
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $returnType    = 'array';

    // Define roles (matching ENUM in migration)
    public const ROLE_ADMIN   = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

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

    public function isTeacher(int $userId): bool
    {
        return $this->hasRole($userId, self::ROLE_TEACHER);
    }

    public function isStudent(int $userId): bool
    {
        return $this->hasRole($userId, self::ROLE_STUDENT);
    }

    /**
     * Get all deleted users (for trash view)
     */
    public function getDeletedUsers()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restore a soft-deleted user
     */
    public function restoreUser(int $id): bool
    {
        // Use builder to directly update deleted_at to NULL
        $db = \Config\Database::connect();
        return $db->table($this->table)
            ->where($this->primaryKey, $id)
            ->where($this->deletedField . ' IS NOT NULL')
            ->update([$this->deletedField => null]);
    }

    /**
     * Get active users only (exclude deleted)
     */
    public function getActiveUsers()
    {
        return $this->findAll();
    }

    /**
     * Override delete method to prevent admin deletion (safeguard at model level)
     * 
     * @param int|array|null $id
     * @param bool $purge
     * @return bool|string
     */
    public function delete($id = null, bool $purge = false)
    {
        // If ID is provided, check if user is admin before deletion
        if ($id !== null) {
            $user = $this->find($id);
            if ($user && strtolower($user['role']) === 'admin') {
                // Prevent admin deletion at model level
                return false;
            }
        }

        // Proceed with normal deletion for non-admin users
        return parent::delete($id, $purge);
    }
}
