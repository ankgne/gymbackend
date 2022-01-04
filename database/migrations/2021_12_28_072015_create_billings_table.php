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
            $table->integer("status_code");
            $table->date("bill_issued_date");
            $table->date("bill_due_date");
            $table->double("due_amount", 15, 4);
            $table->double("bill_amount", 15, 4);
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
        Schema::table("billings", function (Blueprint $table) {
            $table->dropForeign(["account_id"]);
        });
        Schema::dropIfExists("billings");
    }
}
