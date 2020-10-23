<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTmsSelfStatiscticTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_self_statisctic_totals', function (Blueprint $table) {
            $table->integer('question_parent_id')->nullable(false)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_self_statisctic_totals', function (Blueprint $table) {
            $table->dropColumn('question_parent_id');
        });
    }
}
