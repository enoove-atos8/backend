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
        if (!Schema::hasTable('cults'))
        {
            Schema::create('cults', function (Blueprint $table) {

                $table->integer('id', true);
                $table->integer('reviewer_id')->nullable(false);
                $table->string('cult_day')->nullable(false);
                $table->string('cult_date')->nullable(false);
                $table->string('date_transaction_compensation')->nullable(false);
                $table->string('transaction_type')->nullable(false);
                $table->decimal('tithes_amount')->nullable()->default(0);
                $table->decimal('designated_amount')->nullable()->default(0);
                $table->decimal('offers_amount')->nullable()->default(0);
                $table->boolean('deleted')->nullable(false)->default(0);
                $table->string('receipt')->nullable(false);
                $table->string('comments')->nullable();

                $table->timestamps();

            });

            if (!Schema::hasColumn('entries', 'cult_id'))
            {
                Schema::table('entries', function (Blueprint $table)
                {
                    $table->integer('cult_id')->nullable()->after('reviewer_id');

                    $table->foreign('cult_id')
                        ->references('id')
                        ->on('cults');
                });
            }
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
