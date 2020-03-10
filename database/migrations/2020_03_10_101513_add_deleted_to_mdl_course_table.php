<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedToMdlCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->boolean('deleted')->default(0)->nullable(true)->comment('truong check cho TH xoa va restore course');
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
            $table->dropColumn('deleted');
        });
    }
}
