<?php

namespace App\Models;

use Core\Model;

class Reservation extends Model
{
    protected string $table = 'reservations';

    public function getAll(): array
    {
        return $this->fetchAllByQuery(
            "SELECT id, name, email, phone, date, time, guests
             FROM {$this->table}
             ORDER BY date ASC, time ASC"
        );
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone, date, time, guests)
                VALUES (:name, :email, :phone, :date, :time, :guests)";

        $this->query($sql, [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':date' => $data['date'],
            ':time' => $data['time'],
            ':guests' => $data['guests'],
        ]);

        $id = (int) $this->db->lastInsertId();

        return $this->find($id);
    }

    public function find(int $id): array
    {
        return $this->fetchOneByQuery(
            "SELECT id, name, email, phone, date, time, guests
             FROM {$this->table}
             WHERE id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }
}