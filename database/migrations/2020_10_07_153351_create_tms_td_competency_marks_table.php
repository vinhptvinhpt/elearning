<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTdCompetencyMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_td_competency_marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('competency_id')->index()->nullable(false)->comment('id khoa ngoai den bang tms_td_competencies');
            $table->integer('year')->index();
            $table->integer('mark');
            $table->timestamps();

            $table->unique(['competency_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tms_td_competency_marks');
    }
}
