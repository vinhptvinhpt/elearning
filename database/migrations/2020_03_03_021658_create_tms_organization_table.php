<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_organization', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 255);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->tinyInteger('level')->default(1);
            $table->integer('parent_id')->index()->default(0);
            $table->boolean('enabled')->default(1);
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
        Schema::dropIfExists('tms_organization');
    }
}
