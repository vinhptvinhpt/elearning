<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsOrganizationTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_organization_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 255)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('organization_id')->index();
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
        Schema::dropIfExists('tms_organization_teams');
    }
}
