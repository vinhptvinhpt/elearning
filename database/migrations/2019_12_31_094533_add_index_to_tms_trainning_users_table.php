<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsTrainningUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_traninning_users', function (Blueprint $table) {
            $table->index('trainning_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_traninning_users', function (Blueprint $table) {
            $table->dropIndex(['trainning_id']);
            $table->dropIndex(['user_id']);
        });
    }
}
