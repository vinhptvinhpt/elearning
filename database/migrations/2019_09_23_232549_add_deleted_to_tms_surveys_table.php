<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedToTmsSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_surveys', function (Blueprint $table) {
            $table->string('code')->index();
            $table->bigInteger('startdate');
            $table->bigInteger('enddate');
            $table->boolean('isdeleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_surveys', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('startdate');
            $table->dropColumn('enddate');
            $table->dropColumn('isdeleted');
        });
    }
}
