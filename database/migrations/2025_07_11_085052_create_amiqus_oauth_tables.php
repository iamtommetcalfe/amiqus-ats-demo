<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create table for storing OAuth clients
        Schema::create('amiqus_oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('redirect_uri');
            $table->string('scope')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create table for storing OAuth access tokens
        Schema::create('amiqus_oauth_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('amiqus_oauth_clients')->onDelete('cascade');
            $table->string('access_token');
            $table->string('token_type')->default('Bearer');
            $table->integer('expires_in');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // Create table for storing OAuth refresh tokens
        Schema::create('amiqus_oauth_refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_token_id')->constrained('amiqus_oauth_access_tokens')->onDelete('cascade');
            $table->string('refresh_token');
            $table->integer('expires_in');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('amiqus_oauth_refresh_tokens');
        Schema::dropIfExists('amiqus_oauth_access_tokens');
        Schema::dropIfExists('amiqus_oauth_clients');
    }
};
