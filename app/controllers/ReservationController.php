<?php

namespace App\Controllers;

use App\Models\Reservation;
use Core\Controller;
use Throwable;

class ReservationController extends Controller
{
    private Reservation $reservationModel;

    public function __construct()
    {
        $this->reservationModel = new Reservation();
    }

    public function index(): void
    {
        try {
            $reservations = $this->reservationModel->getAll();
            $this->successResponse('Reservations fetched successfully.', $reservations);
        } catch (Throwable $exception) {
            $this->serverError('Unable to fetch reservations.', $exception);
        }
    }

    public function store(): void
    {
        $payload = $this->getRequestData();

        $name = $this->sanitizeString($payload['name'] ?? '');
        $email = filter_var($payload['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = $this->sanitizeString($payload['phone'] ?? '');
        $date = $this->sanitizeString($payload['date'] ?? '');
        $time = $this->sanitizeString($payload['time'] ?? '');
        $guests = filter_var($payload['guests'] ?? null, FILTER_VALIDATE_INT);

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === false) {
            $errors['email'] = 'A valid email is required.';
        }

        if ($phone === '') {
            $errors['phone'] = 'Phone is required.';
        } elseif (!$this->isValidPhone($phone)) {
            $errors['phone'] = 'Phone format is invalid.';
        }

        if (!$this->isValidDate($date)) {
            $errors['date'] = 'Date must be in YYYY-MM-DD format.';
        }

        if (!$this->isValidTime($time)) {
            $errors['time'] = 'Time must be in HH:MM format.';
        }

        if ($guests === false || $guests < 1) {
            $errors['guests'] = 'Guests must be a positive number.';
        } elseif ($guests > 20) {
            $errors['guests'] = 'Guests cannot exceed 20 in a single reservation.';
        }

        if (!empty($errors)) {
            $this->validationError($errors);
        }

        try {
            $created = $this->reservationModel->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'date' => $date,
                'time' => $time,
                'guests' => $guests,
            ]);

            $this->successResponse('Reservation created successfully.', $created, 201);
        } catch (Throwable $exception) {
            $this->serverError('Unable to create reservation.', $exception);
        }
    }
}