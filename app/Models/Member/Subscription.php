<?php

namespace App\Models\Member;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['plan'];

    protected $fillable = [
        "plan_id",
        "account_id",
        "start_date",
        "end_date",
        "charge",
    ];

    /**
     * Mutator for setting the start date in Y-m-d format
     * @param $value
     */
    public function setStartDateAttribute($value)
    {
        $this->attributes["start_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the start date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getStartDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Mutator for setting the end date in Y-m-d format
     * @param $value
     */
    public function setEndDateAttribute($value)
    {
        $this->attributes["end_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the end date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getEndDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

}
