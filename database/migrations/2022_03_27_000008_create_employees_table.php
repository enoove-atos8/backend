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
        Schema::create('employees', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable(false);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('gender');
            $table->string('birth_date', 10);
            $table->string('cpf', 11);
            $table->string('rg', 7);
            $table->string('cell_phone', 11);
            $table->timestamps();


            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
