<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuitTimeToTmsUserDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_user_detail', function (Blueprint $table) {
            $table->integer('quit_time')->default(0)->comment('Thời gian nghỉ việc');
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
            $table->dropColumn('quit_time');
        });
    }
}
