<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsQuestionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_question_datas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('question_id')->index()->comment('Khóa ngoại đến bảng tms_questions');
            $table->longText('content')->comment('Nội dung câu hỏi');
            $table->integer('created_by')->index();
            $table->boolean('status')->default(1)->comment('Trạng thái câu hỏi, được sử dụng or không, 1: được sử dung, 0: không sử dụng');
            $table->string('type_question')->comment('Loại câu hỏi');
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
        Schema::dropIfExists('tms_question_datas');
    }
}
