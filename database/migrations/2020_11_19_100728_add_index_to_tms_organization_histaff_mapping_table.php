<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsOrganizationHistaffMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_organization_histaff_mapping', function (Blueprint $table) {
            $table->string('tms_code')->index()->change();
            $table->string('histaff_code')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_organization_histaff_mapping', function (Blueprint $table) {
            $table->dropColumn('tms_code');
            $table->dropColumn('histaff_code');
        });
    }
}
