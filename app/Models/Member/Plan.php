<?php

namespace App\Models\Member;

use App\Models\Billing;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "fee",
        "validity",
        "status",
    ];

    /**
     * Accessor for returning the status code as active , inactive or suspended
     * @param $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        switch ($value) {
            case 0:
                return "Inactive";
            case 1:
                return "Active";
            default:
                return "Plan Status Unknown";
        }
    }

    /**
     * Get the contact associated with the account.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get bills associated with plan
     */
    public function bills()
    {
        return $this->hasMany(Billing::class);
    }

    /**
     * Active members
     */
    public function scopeActive($query)
    {
        return $query->where("status", 1);
    }

    /**
     * Accessor for returning the created at in m/d/Y format
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }
}
