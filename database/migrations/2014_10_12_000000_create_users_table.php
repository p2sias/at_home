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
            $table->string('name');
            $table->string('surname');
            $table->string('pseudo')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('birthday');
            $table->string('phone')->unique();
            $table->integer('auth_level');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
            $table->string('api_token')->nullable()->default(NULL);
            $table->timestamps();

            $table->engine = 'InnoDB';
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
