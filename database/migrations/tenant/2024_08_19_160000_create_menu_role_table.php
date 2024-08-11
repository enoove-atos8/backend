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
        if (!Schema::hasTable('menu_role'))
        {
            Schema::create('menu_role', function (Blueprint $table) {

                $table->integer('id' )->autoIncrement();
                $table->integer('menu_id' );
                $table->unsignedBigInteger('role_id');

                // Relationships

                $table->foreign('menu_id')
                    ->references('id')
                    ->on('menu');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles');
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
        Schema::dropIfExists('menu_role');
    }
};
