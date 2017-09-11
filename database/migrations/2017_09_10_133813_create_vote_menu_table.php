<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_menu', function (Blueprint $table) {
            $table->string('vote_id');
            $table->integer('no');
            $table->string('text');
            $table->timestamps();

            //primary Key
            $table->foreign('vote_id')->references('id')->on('vote_info');
            $table->primary(['vote_id','no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_menu');
    }
}
