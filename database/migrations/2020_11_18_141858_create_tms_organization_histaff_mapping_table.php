<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsOrganizationHistaffMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_organization_histaff_mapping', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tms_code','255')->nullable(false);
            $table->string('histaff_code','255')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tms_organization_histaff_mapping');
    }
}
