<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogoToTmsTraninningProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_programs', function (Blueprint $table) {
            $table->string('logo')->comment('Ảnh logo chứng chỉ cho khung năng lực');
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
            $table->dropColumn('logo');
        });
    }
}
