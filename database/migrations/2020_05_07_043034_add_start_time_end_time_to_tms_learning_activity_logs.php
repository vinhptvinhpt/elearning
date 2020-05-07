<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartTimeEndTimeToTmsLearningActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_learning_activity_logs', function (Blueprint $table) {
            $table->integer('start_time')->nullable();
            $table->integer('pause_time')->nullable();
            $table->integer('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_learning_activity_logs', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('pause_time');
            $table->dropColumn('end_time');
        });
    }
}
