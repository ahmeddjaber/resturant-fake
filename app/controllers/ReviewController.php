<?php

namespace App\Controllers;

use App\Models\Review;
use Core\Controller;
use Throwable;

class ReviewController extends Controller
{
    private Review $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new Review();
    }

    public function index(): void
    {
        try {
            $reviews = $this->reviewModel->getAll();
            $this->successResponse('Reviews fetched successfully.', $reviews);
        } catch (Throwable $exception) {
            $this->serverError('Unable to fetch reviews.', $exception);
        }
    }

    public function store(): void
    {
        $payload = $this->getRequestData();

        $name = $this->sanitizeString($payload['name'] ?? '');
        $rating = filter_var($payload['rating'] ?? null, FILTER_VALIDATE_INT);
        $comment = $this->sanitizeString($payload['comment'] ?? '');

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($rating === false || $rating < 1 || $rating > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5.';
        }

        if ($comment === '') {
            $errors['comment'] = 'Comment is required.';
        }

        if (!empty($errors)) {
            $this->validationError($errors);
        }

        try {
            $created = $this->reviewModel->create([
                'name' => $name,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            $this->successResponse('Review created successfully.', $created, 201);
        } catch (Throwable $exception) {
            $this->serverError('Unable to create review.', $exception);
        }
    }
}