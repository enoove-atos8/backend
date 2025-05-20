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
                $table->string('card_number')->nullable(false);
                $table->string('expiry_date')->nullable(false);
                $table->string('due_day')->nullable(false);
                $table->string('closing_day')->nullable(false);
                $table->string('status')->nullable(false);
                $table->boolean('active')->default(true);
                $table->boolean('deleted')->default(false);
                $table->string('credit_card_brand')->nullable(false);
                $table->string('person_type')->nullable();
                $table->string('card_holder_name')->nullable();
                $table->decimal('limit', 15)->nullable(false);

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
