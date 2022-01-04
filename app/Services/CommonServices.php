<?php

namespace App\Services;

use Carbon\Carbon;

class CommonServices
{
    /**
     * Returns financial year in format as FY20-21
     * @return string
     */
    public static function getFinancialYear(): string
    {
        $currentDate = Carbon::now();

        if (date_format($currentDate, "m") >= 4) {
            //On or After April (FY would be "current year - next year")
            $financial_year =
                date_format($currentDate, "y") .
                "-" .
                (date_format($currentDate, "y") + 1);
        } else {
            //On or Before March (FY would be  "previous year - current year")
            $financial_year =
                date_format($currentDate, "y") -
                1 .
                "-" .
                date_format($currentDate, "y");
        }

        return "FY" . $financial_year;
    }
}
