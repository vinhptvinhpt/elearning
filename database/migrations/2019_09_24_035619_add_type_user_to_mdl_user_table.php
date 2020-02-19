<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeUserToMdlUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdl_user', function (Blueprint $table) {
            $table->integer('type_user')->nullable()->default(1)->comment('Phân biệt user là của elearning hay diva, elearning: 1, diva: 0');
            $table->string('token_diva')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mdl_user', function (Blueprint $table) {
            $table->dropColumn('type_user');
            $table->dropColumn('token_diva');
        });
    }
}
