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
        if (!Schema::hasTable('unidentified_receipts'))
        {
            Schema::create('unidentified_receipts', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->string('entry_type')->nullable(false);
                $table->decimal('amount')->nullable(false);
                $table->integer('deleted')->nullable(false);
                $table->string('receipt_link')->nullable(false);

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
        Schema::dropIfExists('unidentified_receipts');
    }
};
