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
        Schema::create('request_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amiqus_id')->unique()->comment('ID from Amiqus API');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('presets')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('amiqus_created_at')->nullable();
            $table->timestamp('amiqus_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_templates');
    }
};
