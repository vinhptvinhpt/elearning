<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsOptionalCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_optional_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('course_id')->index()->nullable(false);
            $table->integer('organization_id')->index()->nullable(false);
            $table->timestamps();

            $table->unique(['course_id', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tms_optional_courses');
    }
}
