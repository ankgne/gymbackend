<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Member\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DashboardServices
{
    /**
     * Returns count of new users for a month, year and today
     */
    public static function getNewUsersCount(
        $duration = "monthly",
        $yearOrMonth = null
    ) {
        switch ($duration) {
            case "monthly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->month;
                }
                return Account::query()
                    ->active()
                    ->whereMonth("created_at", $yearOrMonth)
                    ->whereYear("created_at", now()->year)
                    ->count();
            case "yearly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->year;
                }
                return Account::query()
                    ->active()
                    ->whereYear("created_at", $yearOrMonth)
                    ->count();
            case "today":
                return Account::query()
                    ->active()
                    ->whereMonth("created_at", now()->month)
                    ->whereDay("created_at", now()->day)
                    ->count();
        }
    }

    /**
     * Returns count of active users
     */
    public static function getActiveUsersCount()
    {
        return Account::query()
            ->active()
            ->count();
    }

    /**
     * Returns sum of revenue for a month, year and today
     */
    public static function getTotalRevenue(
        $duration = "monthly",
        $yearOrMonth = null
    ) {
        switch ($duration) {
            case "monthly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->month;
                }
                return Transaction::query()
                    ->whereMonth("created_at", $yearOrMonth)
                    ->whereYear("created_at", now()->year)
                    ->sum("transaction_amount");
            case "yearly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->year;
                }
                return Transaction::query()
                    ->whereYear("created_at", $yearOrMonth)
                    ->sum("transaction_amount");
            case "today":
                return Transaction::query()
                    ->whereMonth("created_at", now()->month)
                    ->whereDay("created_at", now()->day)
                    ->count();
        }
    }

    /**
     * Returns payment due amount for a month, year and today
     */
    public static function getDuePayment(
        $duration = "monthly",
        $yearOrMonth = null
    ) {
        switch ($duration) {
            case "monthly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->month;
                }
                return Account::query()
                    ->whereMonth("due_date", $yearOrMonth)
                    ->whereYear("due_date", now()->year)
                    ->where("outstanding_payment", ">", 0)
                    ->sum("outstanding_payment");
            case "yearly":
                if ($yearOrMonth == null) {
                    $yearOrMonth = now()->year;
                }
                return Account::query()
                    ->whereYear("due_date", $yearOrMonth)
                    ->where("outstanding_payment", ">", 0)
                    ->sum("outstanding_payment");
            case "today":
                return Transaction::query()
                    ->whereMonth("due_date", now()->month)
                    ->whereDay("due_date", now()->day)
                    ->where("outstanding_payment", ">", 0)
                    ->sum("outstanding_payment");
        }
    }

    /***
     * Six months attendance data
     * @return \Illuminate\Support\Collection
     */
    public static function sixMonthsAttendanceDataByMonthYear()
    {
        $attendanceData = Attendance::query()
            ->where("attendance_date", ">", now()->subMonths(6))
            ->join("accounts", "attendance.account_id", "=", "accounts.id")
            ->join("customers", "customers.id", "=", "accounts.contact_id")
            ->select(
                DB::raw("count(account_id) as counts"),
                DB::raw("DATE_FORMAT(attendance_date,'%m') as month"),
                DB::raw("DATE_FORMAT(attendance_date,'%y') as year"),
                DB::raw("gender as gender")
            )
            ->groupBy(["month", "gender", "year"])
            ->orderBy("year") // sort by year oldest year first
            ->orderBy("month") // sort by month oldest month first
            ->get()
            ->toArray();

        $attendanceDataCollection = collect($attendanceData);

        return $attendanceDataCollection->mapToGroups(function ($item, $key) {
            return [
                "data" => [
                    "count" => $item["counts"],
                    "month" => $item["month"],
                    "year" => $item["year"],
                    "gender" => $item["gender"],
                ],
            ];
        });
    }

    /***
     * Seven days of attendance data
     * @return \Illuminate\Support\Collection
     */
    public static function sevenDaysAttendanceDataByDay()
    {
        $attendanceData = Attendance::query()
            ->where("attendance_date", ">", now()->subDays(7))
            ->join("accounts", "attendance.account_id", "=", "accounts.id")
            ->join("customers", "customers.id", "=", "accounts.contact_id")
            ->select(
                DB::raw("count(account_id) as counts"),
                DB::raw("DATE_FORMAT(attendance_date,'%d') as day"),
                DB::raw("gender as gender")
            )
            ->groupBy(["day", "gender"])
            ->orderBy("day") // sort by day oldest day first
            ->get()
            ->toArray();

        $attendanceDataCollection = collect($attendanceData);

        return $attendanceDataCollection->mapToGroups(function ($item, $key) {
            return [
                "data" => [
                    "count" => $item["counts"],
                    "day" => $item["day"],
                    "gender" => $item["gender"],
                ],
            ];
        });
    }
}
