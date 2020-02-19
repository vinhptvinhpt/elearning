<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsTrainningCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_trainning_courses', function (Blueprint $table) {
            $table->index('trainning_id');
            $table->index('sample_id');
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_trainning_courses', function (Blueprint $table) {
            $table->dropIndex(['trainning_id']);
            $table->dropIndex(['sample_id']);
            $table->dropIndex(['course_id']);
        });
    }
}
