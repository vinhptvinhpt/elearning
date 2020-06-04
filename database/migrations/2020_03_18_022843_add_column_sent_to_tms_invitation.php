<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSentToTmsInvitation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_invitation', function (Blueprint $table) {
            $table->boolean('sent')->default(0)->comment('Sent 1, Pending 0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_invitation', function (Blueprint $table) {
            $table->dropColumn('sent');
        });
    }
}
