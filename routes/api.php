<?php

use App\Controllers\MenuController;
use App\Controllers\ReservationController;
use App\Controllers\ReviewController;

$router->get('/api/menu', [MenuController::class, 'getAll']);
$router->post('/api/reservation', [ReservationController::class, 'store']);
$router->get('/api/reviews', [ReviewController::class, 'index']);
$router->post('/api/reviews', [ReviewController::class, 'store']);

