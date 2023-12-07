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

            $table->integer('id', true);
            $table->integer('activated')->default('0');
            $table->integer('deleted')->default('0');
            $table->string('avatar')->nullable(true);
            $table->string('full_mame')->nullable(false);
            $table->string('gender')->nullable(false);
            $table->string('cpf')->nullable(false)->unique();
            $table->string('rg')->nullable(false)->unique();
            $table->string('work')->nullable(true);
            $table->string('born_date')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('phone')->nullable(true);
            $table->string('cell_phone')->unique()->nullable(false);
            $table->string('address')->nullable(false);
            $table->string('district')->nullable(false);
            $table->string('city')->nullable(false);
            $table->string('uf')->nullable(false);
            $table->string('marital_status')->nullable(false);
            $table->string('spouse')->nullable(true);
            $table->string('father')->nullable(true);
            $table->string('mother')->nullable(true);
            $table->string('ecclesiastical_function')->nullable(true);
            $table->string('ministries')->nullable(true);
            $table->string('baptism_date')->nullable(false);
            $table->string('blood_type')->nullable(false);
            $table->string('education')->nullable(false);


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
