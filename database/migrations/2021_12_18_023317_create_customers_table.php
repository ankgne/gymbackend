<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("customers", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("type");
            $table->string("gender");
            $table->date("dob");
            $table->string("phone");
            $table->string("email")->unique();
            $table->unique(["phone", "email"]);
            $table->text("address");
            $table->string("city");
            $table->string("state");
            $table->integer("pincode");
            $table->foreignId("created_by")->constrained("users");
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
        Schema::table("customers", function (Blueprint $table) {
            $table->dropForeign(["created_by"]);
        });
        Schema::dropIfExists("customers");
        Schema::enableForeignKeyConstraints();
    }
}
