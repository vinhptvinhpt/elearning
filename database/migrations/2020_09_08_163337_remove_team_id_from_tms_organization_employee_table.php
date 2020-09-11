<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTeamIdFromTmsOrganizationEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_organization_employee', function (Blueprint $table) {
            $table->dropColumn('team_id');
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
            $table->integer('team_id')->nullable(true);
        });
    }
}
