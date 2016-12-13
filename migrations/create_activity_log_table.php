<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function(Blueprint $table) {
            $table->increments('id');
            $table->string('uid',23);
            $table->string('type', 255);
            $table->string('description', 255);
            $table->string('affected_uid', 23);
            $table->text('scope');
            $table->string('mode', 100);
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
        Schema::drop('activity_log');
    }
}
