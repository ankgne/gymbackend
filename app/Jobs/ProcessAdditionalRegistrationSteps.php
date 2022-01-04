<?php

namespace App\Jobs;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Models\Member\Account;
use App\Models\Member\Subscription;
use App\Services\BillingServices;
use App\Services\TransactionServices;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessAdditionalRegistrationSteps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request;
    private $account;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request, Account $account)
    {
        $this->request = $request;
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Subscription::create([
            "plan_id" => $this->request["plan_id"],
            "account_id" => $this->account->id,
            "start_date" => $this->request["plan_start_date"],
            "end_date" => $this->request["plan_end_date"],
            "charge" => $this->request["plan_fee"],
        ]);

        $bill = BillingServices::createUIBillingEntry(
            $this->request,
            $this->account->id
        );

        $transaction = TransactionServices::logUITransaction(
            $this->request,
            $this->account->id,
            $bill->id
        );
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        // TODO to implement notification mechanism
    }
}
