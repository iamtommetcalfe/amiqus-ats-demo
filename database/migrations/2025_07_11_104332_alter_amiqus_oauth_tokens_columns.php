<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify access_token column in amiqus_oauth_access_tokens table
        Schema::table('amiqus_oauth_access_tokens', function (Blueprint $table) {
            $table->text('access_token')->change();
        });

        // Modify refresh_token column in amiqus_oauth_refresh_tokens table
        Schema::table('amiqus_oauth_refresh_tokens', function (Blueprint $table) {
            $table->text('refresh_token')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert access_token column in amiqus_oauth_access_tokens table
        Schema::table('amiqus_oauth_access_tokens', function (Blueprint $table) {
            $table->string('access_token')->change();
        });

        // Revert refresh_token column in amiqus_oauth_refresh_tokens table
        Schema::table('amiqus_oauth_refresh_tokens', function (Blueprint $table) {
            $table->string('refresh_token')->change();
        });
    }
};
