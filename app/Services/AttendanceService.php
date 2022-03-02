<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Member\Account;

class AttendanceService
{
    /**
     * Creates account
     * @param $request
     * @param Contact $contact
     * @return mixed
     */
    public static function createInAttendance($request)
    {
        $time = Helper::nowTimeString();
        $date = Helper::todaysDateString();

        $existingInTime = Attendance::query()
            ->where("account_id", $request->account_id)
            ->where("attendance_date", $date)
            ->latest()
            ->value("in_time");

        $existingOutTime = Attendance::query()
            ->where("account_id", $request->account_id)
            ->where("attendance_date", $date)
            ->latest()
            ->value("out_time");

        //if new attendance record for the day
        //OR
        // second/third (subsequent) attendance record for the day
        if (!$existingInTime || $existingOutTime) {
            return Attendance::create([
                "account_id" => $request->account_id,
                "attendance_date" => $date,
                "in_time" => $time,
            ]);
        } else {
            abort("422", "In time already exists for user");
        }
    }

    public static function createOutAttendance($request)
    {
        $time = Helper::nowTimeString();
        $date = Helper::todaysDateString();

        $existingAttendance = Attendance::query()
            ->where("account_id", $request->account_id)
            ->where("attendance_date", $date)
            ->latest()
            ->first();

        //if existing attendance record exists and out_time record is not available
        if ($existingAttendance && $existingAttendance->out_time == null) {
            $existingAttendance->out_time = $time;

            $existingAttendance->duration = Helper::calculateAttendanceDuration(
                $existingAttendance->out_time,
                $existingAttendance->in_time
            );
            $existingAttendance->save();
            return $existingAttendance;
        } else {
            abort(
                "422",
                "Something went wrong in recording your out-time. Please make sure your in-time of the attendance is marked and retry"
            );
        }
    }

    /***
     * Get list of all active members along with attendance details for today
     */
    /**
     * Returns the list of all active customers
     */
    public static function getActiveCustomersWithAttendance()
    {
        $date = Helper::todaysDateString();
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "attendances" => function ($query) use ($date) {
                $query->where("attendance_date", $date)->latest();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
        ])
            ->active()
            ->get();
    }
}
