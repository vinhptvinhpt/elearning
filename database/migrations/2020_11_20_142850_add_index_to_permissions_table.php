<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name')->index()->change();
            $table->string('guard_name')->index()->change();
            $table->string('url')->index()->change();
            $table->string('permission_slug')->index()->change();
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
            $table->dropColumn('name');
            $table->dropColumn('guard_name');
            $table->dropColumn('url');
            $table->dropColumn('permission_slug');
        });
    }
}
