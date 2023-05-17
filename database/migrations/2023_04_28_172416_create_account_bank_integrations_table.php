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
        Schema::create('account_bank_integrations', function (Blueprint $table) {

            $table->integer('id', true);
            $table->string('username', 255)->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('person_account_type', 255)->nullable(false); #PF/PJ
            $table->string('type_account', 255)->nullable(false); #Current/Savings
            $table->string('agency_number', 255)->nullable(false); #Agency Number
            $table->string('account_number', 255)->nullable(false); #Account Number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_bank_integratios');
    }
};
