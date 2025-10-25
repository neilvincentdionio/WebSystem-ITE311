<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function insertMaterial(array $data)
    {
        return $this->insert($data);
    }

    public function getMaterialsByCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    public function deleteById(int $id): bool
    {
        return (bool) $this->delete($id);
    }
}
