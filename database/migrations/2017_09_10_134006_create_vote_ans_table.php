<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteAnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_ans', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('vote_id');
            $table->integer('no');
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('vote_info');
            $table->primary(['vote_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_ans');
    }
}
