<?php

namespace App\Models;

use Core\Model;

class Menu extends Model
{
    protected string $table = 'menus';

    public function getAll(): array
    {
        return $this->fetchAllByQuery(
            "SELECT id, name, description, price, image, category
             FROM {$this->table}
             ORDER BY category ASC, name ASC"
        );
    }
}