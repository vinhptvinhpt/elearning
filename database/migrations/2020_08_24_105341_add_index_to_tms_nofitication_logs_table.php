<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsNofiticationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_nofitication_logs', function (Blueprint $table) {
            $table->index('target');
            $table->index('sendto');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_nofitication_logs', function (Blueprint $table) {
            $table->dropIndex('target');
            $table->dropIndex('sendto');
            $table->dropIndex('type');
        });
    }
}
