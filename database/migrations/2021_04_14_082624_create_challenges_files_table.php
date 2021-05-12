<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengesFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges_files', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->string('type');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('validation_id');
            $table->timestamps();
            $table->string('user_fileName');
            $table->integer('width')->default(NULL);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('validation_id')->references('id')->on('validations')->onDelete('cascade');

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
        Schema::dropIfExists('validations_files');
    }
}
