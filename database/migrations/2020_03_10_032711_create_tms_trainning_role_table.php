<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTrainningRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_trainning_role', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trainning_id')->comment('Id khung năng lực khóa ngoại đến bảng tms_traninning_programs')->nullable(false)->unsigned();
            $table->integer('role_id')->comment('Id quyền, khóa ngoại đến bảng roles')->nullable(false)->unsigned();
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
        Schema::dropIfExists('tms_trainning_role');
    }
}
