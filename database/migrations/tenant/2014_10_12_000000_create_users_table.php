<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('activated')->default('0');
            $table->string('type')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Called to execute sql file and store procedures

        //DB::unprepared(file_get_contents(''));
        //DB::select('exec my_stored_procedure("Param1", "param2",..)');
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
};
