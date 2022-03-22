<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuizIdToTmsNofiticationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_nofitications', function (Blueprint $table) {
            $table->text('content')->nullable(true)->comment('Lưu thông tin json khi có thông báo liên quan đến quiz, cấu hình thành dữ liệu json');
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
            $table->dropColumn('content');
        });
    }
}
