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
        if (!Schema::hasTable('ecclesiastical_divisions_groups'))
        {
            Schema::create('ecclesiastical_divisions_groups', function (Blueprint $table) {
                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_division_id')->nullable(false);
                $table->integer('parent_group_id')->nullable();
                $table->string('name')->nullable(false);
                $table->string('description')->nullable();
                $table->boolean('financial_transactions_exists')->nullable(false)->default(0);
                $table->boolean('enabled')->nullable(false)->default(1);
                $table->boolean('temporary_event')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                // Relationships
                $table->foreign('ecclesiastical_division_id', 'fk_edg_division')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('parent_group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecclesiastical_divisions_groups');
    }
};
