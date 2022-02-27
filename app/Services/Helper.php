<?php

namespace App\Services;

use Carbon\Carbon;

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

    /**
     * Returns today's date string
     * @return string
     */
    public static function todaysDateString()
    {
        $todaysDate = Carbon::now();
        return $todaysDate->toDateString();
    }
}
