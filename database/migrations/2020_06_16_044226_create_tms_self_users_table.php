<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsSelfUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_self_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type_question');
            $table->integer('self_id')->index();
            $table->integer('question_parent_id')->index();
            $table->integer('section_id')->index();
            $table->integer('question_id')->index();
            $table->integer('answer_id')->index();
            $table->string('answer_content');
            $table->integer('answer_point');
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
        Schema::dropIfExists('tms_self_users');
    }
}
