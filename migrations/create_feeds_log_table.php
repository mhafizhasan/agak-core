<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds_log', function(Blueprint $table) {
            // $table->increments('id');
            // $table->string('uid',23);
            // $table->string('type', 255);
            // $table->string('description', 255);
            // $table->string('affected_uid', 23);
            // $table->text('scope');
            // $table->string('mode', 100);
            // $table->timestamps();
            $table->bigIncrements('id');
            $table->string('nric', 20)->nullable();
            $table->string('uid',23)->nullable();
            $table->string('module', 255)->nullable();
            $table->string('action', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('fuid', 23)->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('feeds_log');
    }
}
