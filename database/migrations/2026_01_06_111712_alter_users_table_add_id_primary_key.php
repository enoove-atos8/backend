<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing primary key on email
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');

        // Add id column as auto increment
        DB::statement('ALTER TABLE users ADD COLUMN id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');

        // Add unique constraint on email + tenant_id combination
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email', 'tenant_id'], 'users_email_tenant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('users_email_tenant_unique');

            // Drop id column and recreate email as primary key
            $table->dropColumn('id');
        });

        // Recreate email as primary key
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (email)');
    }
};
