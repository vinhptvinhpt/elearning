<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToTmsTdCompetencyMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_td_competency_marks', function (Blueprint $table) {
            $table->integer('mark')->comment('diem tong cua KNL import tu excel')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_td_competency_marks', function (Blueprint $table) {
            $table->dropColumn('mark');
        });
    }
}
