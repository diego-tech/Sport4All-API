<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches', function (Blueprint $table){

            $table->foreignId('court_id')->constrained()->nullable();
           /* $table->unsignedBigInteger('court_id')->nullable();
            $table->foreign('court_id')->references('id')->on('courts');*/
        });

        Schema::table('reserves', function (Blueprint $table){

            $table->foreignId('court_id')->constrained()->nullable();
            /*$table->unsignedBigInteger('court_id')->nullable();
            $table->foreign('court_id')->references('id')->on('courts');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    
    }
}
