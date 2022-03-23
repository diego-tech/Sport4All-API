<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->nullable();
            $table->string('name');
            $table->enum('visibility', ['Publico', 'Privado', 'Oculto']);
            $table->integer('people_left');
            $table->string('type');
            $table->text('description');
            $table->double('price');
            $table->string('img');
            $table->date('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->dateTime('final_time');
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
        Schema::dropIfExists('events');
    }
}
