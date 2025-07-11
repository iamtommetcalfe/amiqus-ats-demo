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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('interview_stage_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_at')->nullable(); // For scheduled interviews
            $table->text('feedback')->nullable(); // Feedback from the interviewer
            $table->text('notes')->nullable(); // Additional notes
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Ensure a candidate can only apply once to a job posting
            $table->unique(['candidate_id', 'job_posting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
