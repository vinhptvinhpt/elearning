<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePauseTimeStudyingToTmsLearningActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_learning_activity_logs', function (Blueprint $table) {
            $table->string('url');
            $table->boolean('studying')->default(1)->comment('0 => not studying, 1 => studying');
            $table->dropColumn('pause_time');
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
            $table->integer('pause_time')->nullable();
            $table->dropColumn('studying');
            $table->dropColumn('url');
        });
    }
}
