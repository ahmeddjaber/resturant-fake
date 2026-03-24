<?php

use App\Controllers\MenuController;
use App\Controllers\ReservationController;
use App\Controllers\ReviewController;

$router->get('/', [MenuController::class, 'index']);
$router->get('/menu', [MenuController::class, 'index']);
$router->get('/reservations', [ReservationController::class, 'index']);
$router->get('/reviews', [ReviewController::class, 'index']);

