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
        if (!Schema::hasTable('sync_storage'))
        {
            Schema::create('sync_storage', function (Blueprint $table) {

                $table->id();
                $table->string('tenant');
                $table->string('module');
                $table->string('doc_type');
                $table->string('doc_sub_type');
                $table->unsignedBigInteger('division_id')->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->unsignedBigInteger('payment_category_id')->nullable();
                $table->unsignedBigInteger('payment_item_id')->nullable();
                $table->boolean('is_payment');
                $table->boolean('is_credit_card_purchase');
                $table->date('credit_card_due_date')->nullable();
                $table->integer('number_installments')->nullable();
                $table->date('purchase_credit_card_date')->nullable();
                $table->decimal('purchase_credit_card_amount', 10, 2)->nullable();
                $table->string('status');
                $table->string('path');


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
        Schema::dropIfExists('sync_storage');
    }
};
