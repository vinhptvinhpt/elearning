<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsSurveyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Bảng lưu thông tin người dùng trả lời survey
        Schema::create('tms_survey_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('survey_id')->index();
            $table->integer('question_id')->index();
            $table->integer('answer_id')->index()->nullable(true);
            $table->integer('user_id')->index()->comment('Người chọn đáp án câu hỏi');
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
        Schema::dropIfExists('tms_survey_users');
    }
}
