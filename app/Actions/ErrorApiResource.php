<?php

namespace App\Actions;

use Illuminate\Http\JsonResponse;

class ErrorApiResource extends JsonResponse
{
    public const CODE_SHIFT_NOT_FOUND = 10;
    public const CODE_LOCATION_NOT_FOUND = 20;
    public const CODE_NO_AVAILABLE_SHIFTS = 100;
    public const CODE_BROTHER_REQUIRED = 110;
    public const CODE_SHIFT_NO_LONGER_AVAILABLE = 120;
    public const CODE_SHIFT_NOT_AVAILABLE_YET = 121;
    public const CODE_NOT_ALLOWED = 200;

    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {
        parent::__construct($data, $status, $headers, $options, $json);
    }

    public static function create(string $message, int $errorCode, int $status): self
    {
        return new self(['message' => $message, 'error_code' => $errorCode], $status);
    }
}
