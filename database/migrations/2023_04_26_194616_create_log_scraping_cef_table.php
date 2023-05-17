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
        Schema::create('log_scraping_cef', function (Blueprint $table) {

            $table->integer('id', true);
            $table->dateTime('date_execution')->nullable(false);
            $table->string('status', 255)->nullable(false);
            $table->string('description', 255)->nullable(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_integration_transactions');
    }
};
