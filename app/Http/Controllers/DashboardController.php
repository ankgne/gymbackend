<?php

namespace App\Http\Controllers;

use App\Services\DashboardServices;
use App\Services\Helper;

class DashboardController extends Controller
{
    public function getSixMonthsAttendance()
    {
        try {
            return DashboardServices::sixMonthsAttendanceDataByMonthYear()->toArray();
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }

    public function getSevenDaysAttendance()
    {
        try {
            return DashboardServices::sevenDaysAttendanceDataByDay()->toArray();
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }

    public function getNewUsersCount($duration = "monthly", $yearOrMonth = null)
    {
        try {
            return DashboardServices::getNewUsersCount($duration, $yearOrMonth);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }

    public function getActiveUsersCount()
    {
        try {
            return DashboardServices::getActiveUsersCount();
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }

    public function getMonthlyTotalRevenue()
    {
        try {
            return DashboardServices::getTotalRevenue($duration = "monthly", $yearOrMonth = null);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }

    public function getCurrentMonthPaymentDue()
    {
        try {
            return DashboardServices::getDuePayment($duration = "monthly", $yearOrMonth = null);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Dashboard");
        }
    }
}
