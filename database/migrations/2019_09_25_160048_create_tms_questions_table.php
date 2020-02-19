<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('survey_id')->index();
            $table->string('type_question')->index()->comment('Loại câu hỏi multiplechoice, ddtotext, singlechoice');
            $table->integer('display')->default(1)->index()->comment('Cách trình bày câu hỏi, chỉ áp dụng với câu hỏi chọn đáp án: 1: theo chiều dọc, 0: theo chiều ngang');
            $table->string('name')->index()->comment('Tên câu hỏi');
            $table->longText('content')->comment('Nội dung câu hỏi');
            $table->integer('created_by')->index();
            $table->boolean('status')->default(1)->comment('Trạng thái câu hỏi, được sử dụng or không, 1: được sử dung, 0: không sử dụng');
            $table->integer('total_answer')->comment('Tổng số đáp án của câu hỏi');
            $table->boolean('isdeleted')->default(0)->comment('Xóa mềm câu hỏi');
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
        Schema::dropIfExists('tms_questions');
    }
}
