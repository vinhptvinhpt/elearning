<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_certificate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->default(0);
            $table->string('code');
            $table->bigInteger('timecertificate')->nullable();
            $table->integer('status')->comment('0: chưa cấp, 1: đang đợi cấp, 2: đã cấp');
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
        Schema::dropIfExists('student_certificate');
    }
}
