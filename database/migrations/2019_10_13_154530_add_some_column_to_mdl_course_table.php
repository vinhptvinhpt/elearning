<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnToMdlCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->boolean('is_certificate')->default(0);
            $table->integer('total_date_course')->default(0); //tổng số ngày học của course, áp dụng cho khóa tập trung
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->dropColumn('is_certificate');
            $table->dropColumn('total_date_course');
        });
    }
}
