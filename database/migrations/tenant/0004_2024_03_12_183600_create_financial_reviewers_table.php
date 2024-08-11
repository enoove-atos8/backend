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
        if (!Schema::hasTable('financial_reviewers'))
        {
            Schema::create('financial_reviewers', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->string('full_name')->nullable(false);
                $table->string('reviewer_type')->nullable(false);
                $table->string('avatar')->nullable();
                $table->string('gender')->nullable(false);
                $table->string('cpf')->nullable()->unique();
                $table->string('rg')->nullable()->unique();
                $table->string('email')->unique()->nullable();
                $table->string('cell_phone')->nullable(false);
                $table->integer('activated')->default('0');
                $table->integer('deleted')->default('0');


                $table->rememberToken();
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
        Schema::dropIfExists('members');
    }
};
