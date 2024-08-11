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
        if (Schema::hasTable('members'))
        {
            Schema::create('members', function (Blueprint $table) {
                $table->integer('id', true)->autoIncrement();

                // Relationships with ecclesiastical divisions (ministry, department, organization, event)
                $table->integer('ecclesiastical_divisions_area_id')->nullable();
                $table->boolean('leader')->nullable()->default(0);

                // Status of the member
                $table->boolean('activated')->default(0);
                $table->boolean('deleted')->default(0);

                // Profile information
                $table->string('avatar')->nullable();
                $table->string('full_name')->nullable(false);
                $table->string('gender')->nullable(false);
                $table->string('cpf')->nullable()->unique();
                $table->string('rg')->nullable()->unique();
                $table->string('work')->nullable();

                // Personal and contact details
                $table->string('born_date')->nullable(false);
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('cell_phone')->nullable(false);
                $table->string('address')->nullable(false);
                $table->string('district')->nullable(false);
                $table->string('city')->nullable(false);
                $table->string('uf')->nullable(false);

                // Family and additional information
                $table->string('marital_status')->nullable();
                $table->string('spouse')->nullable();
                $table->string('father')->nullable();
                $table->string('mother')->nullable(false);
                $table->string('ecclesiastical_function')->nullable();

                // Member type and additional information
                $table->string('member_type')->nullable(false);
                $table->string('baptism_date')->nullable();
                $table->string('blood_type')->nullable();
                $table->string('education')->nullable();

                // Remember token for authentication
                $table->rememberToken();

                // Date and time control fields
                $table->timestamps();

                // Relationships with ecclesiastical divisions areas
                $table->foreign('ecclesiastical_divisions_area_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_areas')
                    ->onDelete('set null');
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
