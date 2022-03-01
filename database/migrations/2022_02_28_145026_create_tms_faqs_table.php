<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_faqs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('content')->comment("html")->nullable(true);
            $table->integer('tab_id')->comment("related to tms_faq_tabs")->default(0);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('tms_faqs');
    }
}
