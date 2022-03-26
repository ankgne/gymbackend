<?php

namespace App\Models\Member;

use Carbon\Carbon;
use Database\Factories\Member\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected static function newFactory()
    {
        return SubscriptionFactory::new();
    }
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
        "status",
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

    /**
     * Get the plan associated with the subscription.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Accessor for returning the status as active , inactive or suspended
     * @param $value
     * @return string
     */
    public function getStatusAttribute($value): string
    {
        switch ($value) {
            case 0:
                return "Closed";
            case 1:
                return "Active";
            case 2:
                return "Suspended";
            case 3:
                return "QueuedForUpdate";
            case 4:
                return "Inactive";
            default:
                return "Status Unknown";
        }
    }

    /**
     * Active Subscription
     */
    public function scopeActive($query)
    {
        return $query->where("status", 1);
    }

    /**
     * Active Subscription
     */
    public function scopeQueue($query)
    {
        return $query->where("status", 3);
    }

}
