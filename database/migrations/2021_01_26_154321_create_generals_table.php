<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generals', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('Bongo Bazar');
            $table->string('app_name_bd')->default('বঙ্গ বাজার');
            $table->string('logo_white', 100)->default('image/setup/logo_white.png');
            $table->string('logo_black', 100)->default('image/setup/logo_black.png');
            $table->boolean('maintenance_mode')->default(0)->comment('0=Active, 1=Maintenance');
            $table->dateTime('maintenance_date')->nullable();
            $table->boolean('bangla_language')->default(0)->comment('0=Inactive, 1=Active');
            $table->boolean('default_language')->default(1)->comment('1=English, 2=Bangla');
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
        Schema::dropIfExists('generals');
    }
}
