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
        if (!Schema::hasTable('accounts_files'))
        {
            Schema::create('accounts_files', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('account_id');
                $table->string('original_filename')->nullable(false);
                $table->string('link')->nullable(false);
                $table->string('file_type')->nullable(false);
                $table->integer('version')->default(1);
                $table->string('reference_date')->nullable(false);
                $table->string('status')->nullable(false);
                $table->text('error_message')->nullable();
                $table->boolean('deleted')->default(false);

                $table->timestamps();

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');
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
        Schema::dropIfExists('accounts_files');
    }
};
