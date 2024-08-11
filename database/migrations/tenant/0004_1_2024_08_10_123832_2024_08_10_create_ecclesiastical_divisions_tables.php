<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ecclesiastical_divisions', function (Blueprint $table) {

            $table->integer('id', true)->autoIncrement();
            $table->string('name')->nullable(false);
            $table->string('description')->nullable();
            $table->boolean('enabled')->nullable(false)->default(1);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecclesiastical_divisions');
    }
};
