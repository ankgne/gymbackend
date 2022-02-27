<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("plans", function (Blueprint $table) {
            $table->id();
            $table->string("name");
//            $table->string("value");
            $table->double("fee", 15, 4);
            // validity in days
            $table->integer("validity");
            // default is active
            // 0 inactive
            // 1 active
            // active plans would only flow to UI for new users and edit screens
            $table->integer("status")->default(1);
            $table->integer("description")->nullable(); //for future only
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
        Schema::dropIfExists("plans");
    }
}
