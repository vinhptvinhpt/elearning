<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexCodeToStudentCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_certificate', function (Blueprint $table) {
            $table->string('code')->index()->change();
            $table->string('trainning_id')->index()->change();
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
            $table->dropColumn('code');
            $table->dropColumn('trainning_id');
        });
    }
}
