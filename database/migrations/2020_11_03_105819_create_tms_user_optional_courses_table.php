<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsUserOptionalCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_user_optional_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index()->nullable(false);
            $table->integer('optional_course_ids')->index()->nullable(false);
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
        Schema::dropIfExists('tms_user_optional_courses');
    }
}
