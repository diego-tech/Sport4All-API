<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchs', function (Blueprint $table) {
            $table->id();
            $table->string('QR')->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->foreignId('court_id')->constrained()->nullable();
            $table->double('price_people');
            $table->dateTime('start_dateTime');
            $table->dateTime('end_dateTime');
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
        Schema::dropIfExists('matchs');
    }
}