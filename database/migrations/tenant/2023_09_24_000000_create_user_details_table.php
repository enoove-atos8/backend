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
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable(false);
            $table->string('full_name')->nullable(false);
            $table->string('avatar')->nullable();
            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('birthday')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
