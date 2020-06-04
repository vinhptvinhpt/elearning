<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionToTmsTraninningProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_programs', function (Blueprint $table) {
            $table->bigInteger('time_start')->length(12);
            $table->bigInteger('time_end')->length(12);
            $table->boolean('run_cron')->default(1)->comment('0 => cron off, 1 => cron on');
            $table->boolean('style')->default(0)->comment('0 => KNL thường, 1 => KNL theo khoảng tg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_traninning_programs', function (Blueprint $table) {
            $table->dropColumn('time_start');
            $table->dropColumn('time_end');
            $table->dropColumn('run_cron');
            $table->dropColumn('style');
        });
    }
}
