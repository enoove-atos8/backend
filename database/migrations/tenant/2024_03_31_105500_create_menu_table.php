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
        if (Schema::hasTable('menu'))
        {
            Schema::create('menu', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('parent_id', false)->nullable();
                $table->string('title')->nullable(false);
                $table->string('subtitle')->nullable();
                $table->string('type')->nullable(false);
                $table->string('icon')->nullable();
                $table->string('link')->nullable();


                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
