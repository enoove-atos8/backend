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
        if (!Schema::hasTable('payment_item'))
        {
            Schema::create('payment_item', function (Blueprint $table) {

                $table->integer('id', true);
                $table->integer('payment_category_id');
                $table->string('slug')->unique();
                $table->string('name');
                $table->timestamps();

                $table->foreign('payment_category_id')
                    ->references('id')
                    ->on('payment_category');

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
        Schema::dropIfExists('payment_item');
    }
};
