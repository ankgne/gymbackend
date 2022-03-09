<?php

namespace App\Mail;

use App\Models\Member\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberRegistered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $accountDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accountID)
    {
        // to ensure that this works after DP commit
        $this->afterCommit();

        $this->accountDetails = Account::with([
            "contact",
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
            "bills" => function ($query) {
                $query->latest();
            },
            "transactions" => function ($query) {
                $query->latest();
            },
        ])->find($accountID);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Welcome to " . env("APP_NAME", "Gym app"))->view(
            "emails.registration"
        );
    }
}
