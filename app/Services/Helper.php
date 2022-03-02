<?php

namespace App\Services;

use Carbon\Carbon;

class Helper
{
    public static function exceptionJSON(
        \Exception $exception,
        int $statusCode,
        string $label
    ) {
        return response()->json(
            [
                "message" =>
                    "An exception occurred while performing the action.",
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

    /**
     * Returns today's date string
     * @return string
     */
    public static function nowTimeString()
    {
        $todaysDate = Carbon::now();
        return $todaysDate->toTimeString();
    }

    /**
     * Converts datetime from UCT to GMT
     * @return Carbon|false
     */
    public static function convertTimeFromUTCToGMT($dateTime)
    {
        $date = Carbon::createFromFormat("H:i:s", $dateTime, "UTC");
        $date->setTimezone("Asia/Calcutta");
        return $date->toTimeString();
    }

    /***
     * Duration between start and in time
     */
    public static function calculateAttendanceDuration($finishTime, $startTime)
    {
        $startTime = Carbon::parse($startTime);
        $finishTime = Carbon::parse($finishTime);

        $totalDuration = $finishTime->diff($startTime)->format('%H:%i:%s');;

        return $totalDuration;
    }
}
