<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsManualEnrolLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_manual_enrol_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index()->nullable(false);
            $table->integer('course_id')->index()->nullable(false);
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
        Schema::dropIfExists('tms_manual_enrol_log');
    }
}
