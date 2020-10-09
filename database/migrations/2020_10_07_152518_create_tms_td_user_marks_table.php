<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTdUserMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_td_user_marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('competency_id')->index()->nullable(false)->comment('id khoa ngoai den bang tms_td_competencies');
            $table->integer('user_id')->index()->nullable(false)->comment('id khoa ngoai den bang mdl_user');
            $table->integer('year')->index();
            $table->integer('mark');
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
        Schema::dropIfExists('tms_td_user_marks');
    }
}
