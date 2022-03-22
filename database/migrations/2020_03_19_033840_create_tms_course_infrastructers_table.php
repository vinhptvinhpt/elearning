<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsCourseInfrastructersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_course_infrastructures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('course_id')->nullable(false)->index();
            $table->string('infra_name')->nullable(false)->comment('Tên cở sở vật chất');
            $table->integer('infra_number')->default(0)->comment('Số lượng vật chất');
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
        Schema::dropIfExists('tms_course_infrastructures');
    }
}
