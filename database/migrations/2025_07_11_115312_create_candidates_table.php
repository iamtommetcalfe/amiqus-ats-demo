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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('resume_path')->nullable(); // Path to resume file
            $table->text('cover_letter_path')->nullable(); // Path to cover letter file
            $table->text('notes')->nullable(); // Internal notes about the candidate
            $table->string('source')->nullable(); // Where the candidate came from (e.g., LinkedIn, Indeed, etc.)
            $table->string('current_company')->nullable();
            $table->string('current_position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
