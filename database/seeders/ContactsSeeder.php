<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Billing;
use App\Models\Member\Account;
use App\Models\Member\Contact;
use App\Models\Member\Subscription;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = Account::factory()
            ->count(50)
            ->create();

        foreach ($accounts as $account) {
            Subscription::factory()->create([
                "account_id" => $account->id,
            ]);

            $bill = Billing::factory()->create([
                "account_id" => $account->id,
            ]);

            Transaction::factory()->create([
                "bill_id" => $bill->id,
                "account_id" => $account->id,
            ]);

            Attendance::factory()->count(40)->create([
                "account_id" => $account->id,
            ]);
        }
    }
}
