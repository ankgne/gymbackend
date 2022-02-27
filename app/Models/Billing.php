<?php

namespace App\Models;

use App\Models\Member\Account;
use App\Models\Member\Plan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    use HasFactory;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        "bill_number",
        "account_id",
        "status_code",
        "bill_issued_date",
        "bill_due_date",
        "bill_amount",
        "financial_year",
        "plan_id",
        "prev_due_amount",
        "billing_period"
    ];

    /**
     * Mutator for setting the bill issued date in Y-m-d format
     * @param $value
     */
    public function setBillIssuedDateAttribute($value)
    {
        $this->attributes["bill_issued_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the bill issued date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getBillIssuedDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Mutator for setting the bill due date in Y-m-d format
     * @param $value
     */
    public function setBillDueDateAttribute($value)
    {
        $this->attributes["bill_due_date"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the bill due date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getBillDueDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Accessor for returning the status code as active , inactive or suspended
     * @param $value
     * @return string
     */
    public function getStatusCodeAttribute($value)
    {
        switch ($value) {
            case "0":
                return "Unpaid";
            case "1":
                return "Fully Paid";
            case "2":
                return "Partially Paid";
            default:
                return "Bill Status Unknown";
        }
    }

    /***
     * Gets the transactions associated with a bill
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'bill_id');
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }
}
