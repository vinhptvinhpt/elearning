<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTraninningUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_traninning_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trainning_id')->comment('Id khung năng lực khóa ngoại đến bảng tms_traninning_programs')->nullable(false)->unsigned();
            $table->integer('user_id')->comment('Id khóa học, khóa ngoại đến bảng mdl_user')->nullable(false)->unsigned();
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
        Schema::dropIfExists('tms_traninning_users');
    }
}
