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
        Schema::create('amiqus_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type')->index();
            $table->unsignedBigInteger('loggable_id')->index();
            $table->string('method');
            $table->text('url');
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_headers')->nullable();
            $table->json('response_body')->nullable();
            $table->float('duration')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            // Add index for the polymorphic relationship
            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amiqus_api_logs');
    }
};
