<?php

namespace App\Models;

use Core\Model;

class Review extends Model
{
    protected string $table = 'reviews';

    public function getAll(): array
    {
        return $this->fetchAllByQuery(
            "SELECT id, name, rating, comment, created_at
             FROM {$this->table}
             ORDER BY created_at DESC, id DESC"
        );
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (name, rating, comment, created_at)
                VALUES (:name, :rating, :comment, NOW())";

        $this->query($sql, [
            ':name' => $data['name'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment'],
        ]);

        $id = (int) $this->db->lastInsertId();

        return $this->find($id);
    }

    public function find(int $id): array
    {
        return $this->fetchOneByQuery(
            "SELECT id, name, rating, comment, created_at
             FROM {$this->table}
             WHERE id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }
}