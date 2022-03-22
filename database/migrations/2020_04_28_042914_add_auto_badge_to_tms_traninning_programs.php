<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoBadgeToTmsTraninningPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_programs', function (Blueprint $table) {
            $table->boolean('auto_badge')->default(1)->comment('0 => auto off, 1 => auto on');
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
            $table->dropColumn('auto_badge');
        });
    }
}
