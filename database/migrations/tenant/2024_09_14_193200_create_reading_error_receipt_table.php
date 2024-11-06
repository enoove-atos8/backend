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
        if (!Schema::hasTable('reading_error_receipt'))
        {
            Schema::create('reading_error_receipt', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('group_returned_id')->nullable();
                $table->integer('group_received_id')->nullable();
                $table->string('entry_type')->nullable(false);
                $table->decimal('amount')->nullable();
                $table->string('reason')->nullable();
                $table->string('institution')->nullable();
                $table->boolean('devolution')->default(0);
                $table->integer('deleted')->nullable(false)->default(0);
                $table->string('receipt_link')->nullable(false);

                $table->timestamps();

                $table->foreign('group_received_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('group_returned_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

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
        Schema::dropIfExists('reading_error_receipt');
    }
};
