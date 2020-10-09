<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToTmsTdUserMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_td_user_marks', function (Blueprint $table) {
            $table->integer('mark')->comment('diem hoc vien theo KNL va nam')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_td_user_marks', function (Blueprint $table) {
            $table->dropColumn('mark');
        });
    }
}
