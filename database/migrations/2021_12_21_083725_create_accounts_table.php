<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("accounts", function (Blueprint $table) {
            $table->id();
            $table->string("registration_number")->unique();
            // default is active
            // 0 inactive
            // 1 active
            // 2 suspended
            $table->integer("status")->default(1);
            $table->foreignId("contact_id")->constrained("customers");
            $table->double("outstanding_payment", 15, 4);
            $table->date("due_date");
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
        Schema::table("accounts", function (Blueprint $table) {
            $table->dropForeign(["contact_id"]);
        });
        Schema::dropIfExists("accounts");
    }
}
