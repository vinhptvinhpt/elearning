<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable()->index();
            $table->string('url')->nullable();
            $table->integer('user')->nullable()->index();
            $table->string('ip')->nullable();
            $table->string('action')->nullable();
            $table->text('info',65535)->nullable();
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
        Schema::drop('tms_log');
    }
}
