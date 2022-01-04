<?php

namespace App\Models;

use App\Models\Member\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        "receipt_number",
        "account_id",
        "bill_id",
        "transaction_mode",
        "transaction_type",
        "transaction_date",
        "transaction_amount",
        "transaction_comment",
        "financial_year",
    ];

    /**
     * Mutator for setting the transaction date in Y-m-d format
     * @param $value
     */
    public function setTransactionDateAttribute($value)
    {
        $this->attributes["transaction_date"] = Carbon::parse($value)->format(
            "Y-m-d"
        );
    }

    /**
     * Accessor for returning the transaction date in m/d/Y format
     * @param $value
     * @return string
     */
    public function getTransactionDateAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Accessor for returning the transaction mode as cash, card, paytm, googlepay and others
     * @param $value
     * @return string
     */
    public function getTransactionModeAttribute($value)
    {
        switch ($value) {
            case "0":
                return "Cash";
            case "1":
                return "Card";
            case "2":
                return "PayTM";
            case "3":
                return "GooglePay";
            default:
                return "Others";
        }
    }

    /**
     * Accessor for returning the transaction mode as cash, card, paytm, googlepay and others
     * @param $value
     * @return string
     */
    public function getTransactionTypeAttribute($value)
    {
        switch ($value) {
            case "0":
                return "Payment";
            case "1":
                return "Refund";
            default:
                return "Unknown";
        }
    }

    /**
     * Returns the bill details associated with the transaction
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bill()
    {
        return $this->belongsTo(Billing::class);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }
}
