<?php

namespace App\Models;

use App\Models\Member\Account;
use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        "account_id",
        "attendance_date",
        "in_time",
        "out_time",
        "duration",
    ];

    /**
     * Mutator for setting the attendance date in Y-m-d format
     * @param $value
     */
    public function setAttendanceDateAttribute($value)
    {
        $this->attributes["attendance_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the attendance date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getAttendanceDateAttribute($value): string
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Accessor for returning the attendance in time in H:i:s format
     * @param $value
     * @return Carbon|false
     */
    public function getInTimeAttribute($value)
    {
        return Helper::convertTimeFromUTCToGMT($value);
    }

    /**
     * Accessor for returning the attendance out time in H:i:s format
     * @param $value
     * @return Carbon|false
     */
    public function getOutTimeAttribute($value)
    {
        if ($value == null){
            return null;
        }
        return Helper::convertTimeFromUTCToGMT($value);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }

}
