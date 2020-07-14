<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableMdlCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->integer('last_modify_user')->comment('User who made last modification')->nullable();
            $table->integer('last_modify_time')->comment('Last modification time')->nullable();
            $table->string('last_modify_action')->comment('Last modification action: created, updated, deleted, restored. Exclude: viewed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mdl_course', function (Blueprint $table) {
            $table->dropColumn('last_modify_user');
            $table->dropColumn('last_modify_time');
            $table->dropColumn('last_modify_action');
        });
    }
}
