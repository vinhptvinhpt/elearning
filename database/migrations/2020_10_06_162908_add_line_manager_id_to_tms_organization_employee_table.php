<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineManagerIdToTmsOrganizationEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_organization_employee', function (Blueprint $table) {
            $table->integer('line_manager_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_organization_employee', function (Blueprint $table) {
            $table->dropColumn('line_manager_id');
        });
    }
}
