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
        if (!Schema::hasTable('ecclesiastical_divisions_settings'))
        {
            Schema::create('ecclesiastical_divisions_settings', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_divisions_id')->nullable(false);

                $table->timestamps();

                $table->foreign('ecclesiastical_divisions_id', 'fk_eds_ecclesiastical_divisions_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

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
        Schema::dropIfExists('ecclesiastical_divisions_settings');
    }
};
