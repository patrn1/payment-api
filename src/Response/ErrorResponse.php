<?php

namespace App\Response;

class ErrorResponse extends Response
{
    public ?bool $success = false;

    public function __construct(public ?array $errors = [], ?int $code = 400)
    {
        parent::__construct($this->data, $errors, $this->success, $code);
    }
}
