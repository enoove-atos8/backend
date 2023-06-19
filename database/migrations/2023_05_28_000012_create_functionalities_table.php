<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('functionalities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('plan_id')->nullable(false);
            $table->string('name')->unique()->nullable(false);
            $table->string('display_name')->unique()->nullable(false);
            $table->string('description')->nullable();
            $table->boolean('activated')->nullable(false);
            $table->timestamps();

            // Relationships

            $table->foreign('plan_id')
                ->references('id')
                ->on('plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('functionalities');
    }
}
