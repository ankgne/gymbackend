<?php

namespace App\Services;

class Helper
{
    public static function exceptionJSON(\Exception $exception, int $statusCode, string $label)
    {
        return response()->json(
            [
                "message" => "An exception occurred while performing the action.",
                "errors" => [
                    "$label" => [$exception->getMessage()],
                ],
            ],
            $statusCode
        );
    }
}
