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
        Schema::create('members', function (Blueprint $table) {

            $table->integer('id', true)->autoIncrement();
            $table->integer('activated')->default('0');
            $table->integer('deleted')->default('0');
            $table->string('avatar')->nullable();
            $table->string('full_name')->nullable(false);
            $table->string('gender')->nullable(false);
            $table->string('cpf')->nullable()->unique();
            $table->string('rg')->nullable()->unique();
            $table->string('work')->nullable();
            $table->string('born_date')->nullable(false);
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('cell_phone')->unique()->nullable(false);
            $table->string('address')->nullable(false);
            $table->string('district')->nullable(false);
            $table->string('city')->nullable(false);
            $table->string('uf')->nullable(false);
            $table->string('marital_status')->nullable();
            $table->string('spouse')->nullable();
            $table->string('father')->nullable(false);
            $table->string('mother')->nullable(false);
            $table->string('ecclesiastical_function')->nullable();
            $table->string('ministries')->nullable();
            $table->string('baptism_date')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('education')->nullable();


            $table->rememberToken();
            $table->timestamps();
        });
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
