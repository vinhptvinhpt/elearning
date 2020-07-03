<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsSelfQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_self_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('self_id')->index();
            $table->string('type_question');
            $table->longText('content');
            $table->integer('created_by')->index();
            $table->boolean('isdeleted')->default(0);
            $table->string('other_data')->default("");
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
        Schema::dropIfExists('tms_self_questions');
    }
}
