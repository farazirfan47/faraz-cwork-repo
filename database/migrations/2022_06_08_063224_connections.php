<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->increments("id");
            $table->bigInteger('request_from')->unsigned();
            $table->bigInteger('request_to')->unsigned();
            $table->enum('status', ["REQUESTED", "ACCEPTED"]);
            $table->foreign('request_from')->references('id')->on('users');
            $table->foreign('request_to')->references('id')->on('users');
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
        Schema::drop('connections');
    }
};
