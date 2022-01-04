<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("subscriptions", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("plan_id")
                ->nullable()
                ->constrained("plans");
            $table
                ->foreignId("account_id")
                ->nullable()
                ->constrained("accounts");
            $table->date("start_date");
            $table->date("end_date");
            $table->double("charge", 15, 4);
            // default is active
            // 0 closed - closed by member
            // 1 active
            // 2 suspended - due to non-payment
            $table->integer("status")->default(1);
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
        Schema::disableForeignKeyConstraints();
        Schema::table("subscriptions", function (Blueprint $table) {
            $table->dropForeign(["plan_id"]);
        });
        Schema::table("subscriptions", function (Blueprint $table) {
            $table->dropForeign(["account_id"]);
        });
        Schema::dropIfExists("subscriptions");
        Schema::enableForeignKeyConstraints();
    }
}
