<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToTmsSaleRoomUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_sale_room_user', function (Blueprint $table) {
            $table->string('type')->default('pos')->comment('type = pos sale_room_id là id điểm bán. type = agents sale_room_id là id đại lý');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_sale_room_user', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
