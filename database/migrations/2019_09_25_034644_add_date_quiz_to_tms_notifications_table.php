<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateQuizToTmsNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_nofitications', function (Blueprint $table) {
            $table->bigInteger('date_quiz')->nullable(true)->default(0)->comment('Dùng khi notification liên quan đến quiz, có thể là thời gian bắt đầu làm quiz hoặc kết thúc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_nofitications', function (Blueprint $table) {
            $table->dropColumn('date_quiz');
        });
    }
}
