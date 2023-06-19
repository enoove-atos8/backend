<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChurchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('churches', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('tenant_id')->nullable(false);
            $table->integer('plan_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->boolean('activated')->nullable(false);
            $table->string('doc_type')->nullable(false);
            $table->string('doc_number')->nullable(false);
            $table->timestamps();

            // Relationships

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants');

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
        Schema::dropIfExists('churches');
    }
}
