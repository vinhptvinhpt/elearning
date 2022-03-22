<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModuleToTmsLearningActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_learning_activity_logs', function (Blueprint $table) {
            $table->dropColumn('activity');
            $table->integer('activity_id');
            $table->integer('module_id');
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
            $table->dropColumn('activity_id');
            $table->dropColumn('module_id');
            $table->string('activity');
        });
    }
}
