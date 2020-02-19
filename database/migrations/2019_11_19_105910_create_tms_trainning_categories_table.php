<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsTrainningCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tms_traninning_courses');
        Schema::create('tms_trainning_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trainning_id')->comment('Id khung năng lực khóa ngoại đến bảng tms_traninning_programs')->nullable(false)->unsigned();
            $table->integer('category_id')->comment('Id danh mục khóa học, khóa ngoại đến bảng mdl_category')->nullable(false)->unsigned();
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
        Schema::dropIfExists('tms_trainning_categories');
    }
}
