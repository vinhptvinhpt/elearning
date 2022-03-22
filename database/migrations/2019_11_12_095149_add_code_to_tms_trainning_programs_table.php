<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToTmsTrainningProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_programs', function (Blueprint $table) {
            $table->string('code')->nullable(false)->comment('Mã khung năng lực');
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
            $table->dropColumn('code');
        });
    }
}
