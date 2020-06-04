<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalUnreadMsgToTmsUserDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_user_detail', function (Blueprint $table) {
            $table->integer('total_unread_msg')->default(0);
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
            $table->dropColumn('total_unread_msg');
        });
    }
}
