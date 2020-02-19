<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsSurveyUserViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Bảng lưu thông tin số người đã view survey
        Schema::create('tms_survey_user_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('survey_id')->index();
            $table->integer('user_id')->index()->comment('Người view survey');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tms_survey_user_views');
    }
}
