<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_user_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index();
            $table->string('fullname')->nullable();
            $table->string('cmtnd')->nullable();
            $table->date('dob')->nullable();
            $table->string('avatar')->nullable();
            $table->string('email',100)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('address')->nullable();
            $table->string('city',120)->nullable();
            $table->text('description')->nullable();
            $table->date('last_login_at')->nullable();
            $table->string('last_login_ip',20)->nullable();
            $table->integer('deleted')->default('0');
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
        Schema::dropIfExists('tms_user_detail');
    }
}
