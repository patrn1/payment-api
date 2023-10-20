<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class Response extends JsonResponse
{
    public function __construct(
        $data = [],
        public ?array $errors = [],
        public ?bool $success = true,
        int $code = 200
    ) {
        $jsonResponse = [
            'data' => $data,
            'errors' => $errors,
            'success' => $success,
        ];
        parent::__construct($jsonResponse, $code);
    }
}
