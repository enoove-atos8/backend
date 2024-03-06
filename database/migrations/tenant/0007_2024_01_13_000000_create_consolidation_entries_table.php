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
        Schema::create('consolidation_entries', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('date')->nullable(false);
            $table->boolean('consolidated')->nullable(false)->default(false);
            $table->decimal('designated_amount')->nullable(false)->default(0);
            $table->decimal('offers_amount')->nullable(false)->default(0);
            $table->decimal('tithe_amount')->nullable(false)->default(0);
            $table->decimal('total_amount')->nullable(false)->default(0);
            $table->decimal('monthly_target')->nullable(false)->default(0);

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
        Schema::dropIfExists('consolidation_entries');
    }
};
