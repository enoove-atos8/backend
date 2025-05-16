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
    public function up()
    {
        if (!Schema::hasTable('cards'))
        {
            Schema::create('cards', function (Blueprint $table) {

                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('card_number')->nullable();
                $table->date('expiry_date')->nullable();
                $table->date('closing_date')->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('active')->default(true);
                $table->string('credit_card_brand')->nullable();
                $table->string('person_type')->nullable();
                $table->string('card_holder_name')->nullable();
                $table->decimal('limit', 15)->nullable();

                $table->timestamps();
            });

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
