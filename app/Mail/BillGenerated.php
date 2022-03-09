<?php

namespace App\Mail;

use App\Models\Billing;
use App\Models\Member\Account;
use App\Models\Member\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillGenerated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $accountDetails;
    public $bill;
    public $subscription;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accountID, Billing $bill, Subscription $subscription)
    {
        // to ensure that this works after DP commit
        $this->afterCommit();

        $this->accountDetails = Account::with([
            "contact",
        ])->find($accountID);

        $this->bill = $bill;
        $this->subscription = $subscription;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Your Gym Bill for billing period " . $this->bill->billing_period . " is ready")->view(
            "emails.billGenerated"
        );
    }
}
