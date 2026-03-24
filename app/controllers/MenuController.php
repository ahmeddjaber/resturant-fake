<?php

namespace App\Controllers;

use App\Models\Menu;
use Core\Controller;
use Throwable;

class MenuController extends Controller
{
    private Menu $menuModel;

    public function __construct()
    {
        $this->menuModel = new Menu();
    }

    public function index(): void
    {
        try {
            $menus = $this->menuModel->getAll();
            $this->successResponse('Menu items fetched successfully.', $menus);
        } catch (Throwable $exception) {
            $this->serverError('Unable to fetch menu items.', $exception);
        }
    }

    public function getAll(): void
    {
        $this->index();
    }
}