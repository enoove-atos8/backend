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
        if (!Schema::hasTable('accounts'))
        {
            Schema::create('accounts', function (Blueprint $table) {

                $table->id();
                $table->string('account_type')->nullable(false);
                $table->string('bank_name')->nullable(false);
                $table->string('agency_number')->nullable(false);
                $table->string('account_number')->nullable(false);
                $table->boolean('activated')->default(true);

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
        Schema::dropIfExists('accounts');
    }
};
