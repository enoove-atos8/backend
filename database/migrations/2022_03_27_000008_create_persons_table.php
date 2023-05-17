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
        Schema::create('persons', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable(false);
            $table->string('first_name', 255)->nullable(false);
            $table->string('last_name', 255)->nullable(false);
            $table->string('avatar')->default('assets/images/avatars/rafael.jpg');
            $table->string('gender')->nullable(false);
            $table->string('birth_date', 10)->nullable(false);
            $table->string('cpf', 11)->nullable();
            $table->string('rg', 7)->nullable();
            $table->string('cell_phone', 11)->nullable(false);
            $table->string('ministry', 255)->nullable(false);
            $table->string('department', 255)->nullable(false);
            $table->string('responsibility', 255)->nullable(false);
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
        Schema::dropIfExists('persons');
    }
};
