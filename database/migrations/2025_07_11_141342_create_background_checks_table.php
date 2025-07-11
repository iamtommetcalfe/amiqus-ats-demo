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
        Schema::create('background_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('request_template_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('amiqus_record_id')->comment('ID of the record in Amiqus API');
            $table->unsignedBigInteger('amiqus_client_id')->comment('ID of the client in Amiqus API');
            $table->string('status')->nullable();
            $table->string('perform_url')->nullable();
            $table->json('response_data')->nullable()->comment('Full response data from Amiqus API');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_checks');
    }
};
