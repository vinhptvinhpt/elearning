<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsTrainningGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_trainning_groups', function (Blueprint $table) {
            $table->integer('trainning_id')->index()->change();
            $table->integer('group_id')->index()->change();
            $table->integer('type')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_trainning_groups', function (Blueprint $table) {
            $table->dropColumn('trainning_id');
            $table->dropColumn('group_id');
            $table->dropColumn('type');
        });
    }
}
