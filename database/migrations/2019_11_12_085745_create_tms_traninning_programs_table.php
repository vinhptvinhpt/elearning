<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTraninningProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Khung năng lực
        Schema::create('tms_traninning_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Tên khung năng lực được hiểu là vị trí của người dùng')->nullable(false);
            $table->boolean('deleted')->default(0);
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
        Schema::dropIfExists('tms_traninning_programs');
    }
}
