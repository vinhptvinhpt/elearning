<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultAccessIpValueToMdlCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->string('access_ip')->default('{"list_access_ip":[]}')->change();
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
            $table->string('access_ip')->default('')->change();
        });
    }
}
