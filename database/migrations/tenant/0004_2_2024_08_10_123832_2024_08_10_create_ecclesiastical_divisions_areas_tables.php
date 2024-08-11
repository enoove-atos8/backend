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
        if (!Schema::hasTable('ecclesiastical_divisions_areas'))
        {
            Schema::create('ecclesiastical_divisions_areas', function (Blueprint $table) {
                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_division_id')->nullable(false);


                // For hierarchically related areas, such as departments in ministries.
                $table->integer('parent_area_id')->nullable();

                // Basic data
                $table->string('name')->nullable(false);
                $table->string('description')->nullable();

                // Common fields
                $table->boolean('financial_transactions_exists')->nullable(false)->default(0);
                $table->boolean('departments_exists')->nullable(false)->default(0);
                $table->boolean('events_exists')->nullable(false)->default(0);
                $table->boolean('organizations_exists')->nullable(false)->default(0);
                $table->boolean('enabled')->nullable(false)->default(1);

                // Specified fields of events
                $table->boolean('ministry_linked')->nullable();
                $table->boolean('temporary_event')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                // Relationships
                $table->foreign('ecclesiastical_division_id', 'fk_eda_division')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('parent_area_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_areas');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecclesiastical_divisions_areas');
    }
};
