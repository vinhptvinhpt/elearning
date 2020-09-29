<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoRunToStudentCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_certificate', function (Blueprint $table) {
            $table->integer('auto_run')->nullable(true)->default(1)->comment('Field cho TH chung chi cap tay, ko qua cron');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_certificate', function (Blueprint $table) {
            $table->dropColumn('auto_run');
        });
    }
}
