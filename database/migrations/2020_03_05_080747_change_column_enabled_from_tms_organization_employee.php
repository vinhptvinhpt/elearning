<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnEnabledFromTmsOrganizationEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_organization_employee', function (Blueprint $table) {
            $table->boolean('enabled')->default(1)->change();
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
            $table->boolean('enabled')->default(null)->change();
        });
    }
}
