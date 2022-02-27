<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("transactions", function (Blueprint $table) {
            $table->id();
            $table->string("receipt_number");
            $table
                ->foreignId("account_id")
                ->nullable()
                ->constrained("accounts");
            $table
                ->foreignId("bill_id")
                ->nullable()
                ->constrained("billings");
            // 0 cash
            // 1 card
            // 2 paytm
            // 3 googlepay
            // 10 others
            $table->integer("transaction_mode");
            // 0 payment
            // 1 refund
            $table->integer("transaction_type");
            $table->date("transaction_date");
            $table->double("transaction_amount", 15, 4);
            $table->double("due_amount_before_transaction", 15, 4);
            $table->double("due_amount_after_transaction", 15, 4);
            $table->text("transaction_comment");
            $table->string("financial_year");
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
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropForeign(["account_id"]);
            $table->dropForeign(["bill_id"]);
        });
        Schema::dropIfExists("transactions");
    }
}
