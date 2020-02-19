<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmsDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tms_device', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('imei', 500)->index()->comment('Device imei unique cho mỗi device, có thể generate tự động hoặc thủ công');
            $table->string('token', 500)->nullable()->comment('Device token được generate bởi nhà cung cấp như gcm, apns, firebase...');
            $table->string('type', 20)->index()->comment('android, ios');
            $table->boolean('is_active')->default(1)->index()->comment('Tắt, bật notification');
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
        Schema::dropIfExists('tms_device');
    }
}
