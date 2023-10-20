<?php

namespace App\Response;

class SuccessResponse extends Response
{
    public ?bool $success = true;

    public ?array $errors = [];

    public function __construct(public $data = [], ?int $code = 200)
    {
        parent::__construct($data, $this->errors, $this->success, $code);
    }
}
