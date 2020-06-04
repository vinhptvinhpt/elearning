<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('url','255')->nullable();
            $table->string('method','255')->nullable();
            $table->string('description','255')->nullable();
            $table->string('group_slug','255')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('method');
            $table->dropColumn('description');
            $table->dropColumn('group_slug');
        });
    }
}
