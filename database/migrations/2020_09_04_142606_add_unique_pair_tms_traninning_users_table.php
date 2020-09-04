<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniquePairTmsTraninningUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_users', function (Blueprint $table) {
            $table->unique(['trainning_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //tms_traninning_users_trainning_id_user_id_unique
        Schema::table('tms_traninning_users', function(Blueprint $table) {
            $table->dropUnique('tms_traninning_users_trainning_id_user_id_unique');
        });
    }
}
