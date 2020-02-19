<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_certificate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path')->comment('Đường dẫn ảnh');
            $table->text('name')->comment('Tên ảnh');
            $table->text('description')->comment('Mô tả mẫu chứng chỉ');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('image_certificate');
    }
}
