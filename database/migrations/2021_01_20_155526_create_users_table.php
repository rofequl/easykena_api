<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(0)->comment('0=Normal User, 1=Admin');
            $table->string('name')->nullable();
            $table->string('photo', 100)->default('image/user/user.png');
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->integer('permission_id')->nullable();
            $table->string('address')->unique()->nullable();
            $table->string('password')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender', 10)->nullable();
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
        Schema::dropIfExists('users');
    }
}
