<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTrainningGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_trainning_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trainning_id')->comment('Id khung năng lực khóa ngoại đến bảng tms_traninning_programs')->nullable(false)->unsigned();
            $table->integer('group_id')->nullable(false)->unsigned();
            $table->integer('type')->default(0)->comment('0 => group_id as id roles table,1 => group_id as id tms_organization table');
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
        Schema::dropIfExists('tms_trainning_groups');
    }
}
