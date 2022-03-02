<?php

namespace App\Models\Member;


use App\Models\Attendance;
use App\Models\Billing;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        "registration_number",
        "contact_id",
        "due_date",
        "outstanding_payment",
        "financial_year",
        "status"
    ];

    /**
     * Mutator for setting the Date of birth date in Y-m-d format
     * @param $value
     */
    public function setDueDateAttribute($value)
    {
        $this->attributes["due_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the date of birth in m/d/Y format
     * @param $value
     * @return string
     */
    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
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

    /**
     * Accessor for returning the status as active , inactive or suspended
     * @param $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        switch ($value) {
            case "0":
                return "Inactive";
            case "1":
                return "Active";
            case "2":
                return "Suspended";
            default:
                return "Status Unknown";
        }
    }

    /**
     * Active members
     */
    public function scopeActive($query)
    {
        return $query->where("status", 1);
    }

    /**
     * Inactive members
     */
    public function scopeInactive($query)
    {
        return $query->where("status", 0);
    }

    /**
     * Get the contact associated with the account.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the subscriptions associated with the account.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class,"account_id");
    }

    /**
     * Get the billings associated with the account.
     */
    public function bills()
    {
        return $this->hasMany(Billing::class,"account_id");
    }

    /**
     * Get the transactions associated with the account.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class,"account_id");
    }

    /**
     * Get the attendance associated with the account.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class,"account_id");
    }
}
