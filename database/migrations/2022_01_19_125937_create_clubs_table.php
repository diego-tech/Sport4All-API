<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
<<<<<<< HEAD
            $table->string('description')->nullable();
            $table->string('club_img')->nullable();
            $table->string('club_banner')->nullable();
            $table->string('direction')->nullable();
            $table->string('tlf')->nullable();
=======
            $table->string('description');
            $table->string('club_img');
            $table->string('club_banner');
            $table->string('direction');
            $table->string('tlf');
>>>>>>> diego
            $table->string('email')->unique();
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
        Schema::dropIfExists('clubs');
    }
}
