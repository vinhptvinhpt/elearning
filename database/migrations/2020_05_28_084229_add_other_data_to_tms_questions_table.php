<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtherDataToTmsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_questions', function (Blueprint $table) {
            $table->string('other_data')->nullable(true)->default('{min:0,max:1}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_questions', function (Blueprint $table) {
            $table->dropColumn('other_data');
        });
    }
}
