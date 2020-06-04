<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCourseCompletion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_completion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->default(0);
            $table->integer('courseid')->default(0);
            $table->bigInteger('timecompleted')->nullable();
            $table->bigInteger('timeenrolled')->nullable();
            $table->decimal('finalgrade')->default(0);
            $table->integer('iscoursefinal');
            $table->rememberToken();
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
        Schema::dropIfExists('course_completion');
    }
}
