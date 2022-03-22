<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToTmsSurveyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_survey_users', function (Blueprint $table) {
            $table->string('type_question')->index();
            $table->longText('content_answer')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_survey_users', function (Blueprint $table) {
            $table->dropColumn('type_question');
            $table->dropColumn('content_answer');
        });
    }
}
