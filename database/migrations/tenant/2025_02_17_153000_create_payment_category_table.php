<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable('payment_category'))
        {
            Schema::create('payment_category', function (Blueprint $table) {

                $table->integer('id', true);
                $table->string('tenant');
                $table->string('payment_category_slug')->unique();
                $table->string('payment_category_name');
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
        Schema::dropIfExists('payment_category');
    }
};
