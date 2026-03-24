<?php

namespace Core;

use PDO;
use PDOStatement;

class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function query(string $sql, array $bindings = []): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    protected function fetchAllByQuery(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    protected function fetchOneByQuery(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetch() ?: [];
    }
}