<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("billings", function (Blueprint $table) {
            $table->id();
            $table->string("bill_number");
            $table
                ->foreignId("account_id")
                ->nullable()
                ->constrained("accounts");
            // 0 unpaid
            // 1 fully paid
            // 2 partial paid
            $table
                ->foreignId("plan_id") // plan ID is being stored so that we can determine what plan was going on at the time of bill generation
                ->constrained("plans");
            $table->integer("status_code");
            $table->date("bill_issued_date");
            $table->date("bill_due_date");
            $table->double("prev_due_amount", 15, 4); // get the outstanding_payment from accounts table and in case of new registration it will zero
            $table->double("bill_amount", 15, 4); // total amount - plan fees + previous outstanding amount
            $table->string("financial_year");
            $table->string("billing_period"); //billing period (based on subscription start and end date) in billing table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("billings", function (Blueprint $table) {
            $table->dropForeign(["account_id"]);
        });
        Schema::dropIfExists("billings");
    }
}
