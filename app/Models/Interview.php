<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'job_posting_id',
        'interview_stage_id',
        'scheduled_at',
        'feedback',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the candidate for this interview.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the job posting for this interview.
     */
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the interview stage for this interview.
     */
    public function interviewStage()
    {
        return $this->belongsTo(InterviewStage::class);
    }

    /**
     * Scope a query to only include interviews for a specific job posting.
     */
    public function scopeForJobPosting($query, $jobPostingId)
    {
        return $query->where('job_posting_id', $jobPostingId);
    }

    /**
     * Scope a query to only include interviews for a specific candidate.
     */
    public function scopeForCandidate($query, $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope a query to only include interviews at a specific stage.
     */
    public function scopeAtStage($query, $stageId)
    {
        return $query->where('interview_stage_id', $stageId);
    }
}
