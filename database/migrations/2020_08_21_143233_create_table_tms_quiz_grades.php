<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTmsQuizGrades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_quiz_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('userid')->index();
            $table->string('email',100)->nullable();
            $table->bigInteger('courseid')->nullable()->comment('Id of Toeic course');
            $table->bigInteger('listening')->nullable()->comment('Listening grade');
            $table->bigInteger('reading')->nullable()->comment('Reading grade');
            $table->bigInteger('total')->nullable()->comment('Total grade');
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
        Schema::dropIfExists('tms_quiz_grades');
    }
}
