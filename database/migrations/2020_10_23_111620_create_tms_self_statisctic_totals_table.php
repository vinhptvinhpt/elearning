<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsSelfStatiscticTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_self_statisctic_totals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type_question');
            $table->integer('self_id')->index();
            $table->integer('section_id')->index();
            $table->integer('user_id')->index();
            $table->integer('total_point');
            $table->double('avg_point');
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
        Schema::dropIfExists('tms_self_statisctic_totals');
    }
}
