<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeacherInfoToTmsUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_user_detail', function (Blueprint $table) {
            $table->integer('sex')->default(1);
            $table->string('code')->nullable();
            $table->integer('time_start')->default(0);
            $table->integer('working_status')->default(0);
            $table->integer('department')->nullable();
            $table->string('department_type','20')->nullable();
            $table->integer('confirm')->default(0);
            $table->integer('confirm_address')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_user_detail', function (Blueprint $table) {
            $table->dropColumn('sex');
            $table->dropColumn('code');
            $table->dropColumn('time_start');
            $table->dropColumn('working_status');
            $table->dropColumn('department');
            $table->dropColumn('department_type');
            $table->dropColumn('confirm');
            $table->dropColumn('confirm_address');
        });
    }
}
