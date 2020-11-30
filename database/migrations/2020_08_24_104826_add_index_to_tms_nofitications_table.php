<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsNofiticationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_nofitications', function (Blueprint $table) {
            $table->index('sendto');
            $table->index('target');
            $table->index('status_send');
            $table->index('course_id');
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
        Schema::table('tms_nofitications', function (Blueprint $table) {
            $table->dropIndex('sendto');
            $table->dropIndex('target');
            $table->dropIndex('status_send');
            $table->dropIndex('course_id');
            $table->dropIndex('type');
        });
    }
}
