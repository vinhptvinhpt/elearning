<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTmsTrainningCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tms_trainning_categories', function (Blueprint $table) {
            $table->index('trainning_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tms_trainning_categories', function (Blueprint $table) {
            $table->dropIndex(['trainning_id']);
            $table->dropIndex(['category_id']);
        });
    }
}
