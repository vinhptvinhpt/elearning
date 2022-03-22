<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSomeColumnToCourseCompletionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_completion', function (Blueprint $table) {
            $table->bigInteger('timeenrolled')->nullable(true)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_completion', function (Blueprint $table) {
            $table->dropColumn('timeenrolled');
            $table->dropColumn('iscoursefinal');
        });
    }
}
