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
        if (!Schema::hasTable('cult_financial_data'))
        {
            Schema::create('cult_financial_data', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->string('date_cult')->nullable(false);
                $table->string('cult_period')->nullable(false);
                $table->string('date_deposit')->nullable(false);
                $table->decimal('amount')->nullable(false);
                $table->boolean('pending_receipt')->nullable(false)->default(1);
                $table->string('receipt_link')->nullable(false);
                $table->string('comments')->nullable(false);

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
        Schema::dropIfExists('cults');
    }
};
