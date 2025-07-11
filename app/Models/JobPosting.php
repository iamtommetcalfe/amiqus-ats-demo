<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'department',
        'employment_type',
        'salary_min',
        'salary_max',
        'status',
        'posted_at',
        'closes_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'posted_at' => 'date',
        'closes_at' => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    /**
     * Get the interviews for the job posting.
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the candidates who have applied for this job posting.
     */
    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'interviews')
            ->withPivot('interview_stage_id', 'scheduled_at', 'feedback', 'notes', 'status')
            ->withTimestamps();
    }

    /**
     * Get the number of applicants for this job posting.
     */
    public function getApplicantsCountAttribute()
    {
        return $this->interviews()->count();
    }

    /**
     * Scope a query to only include open job postings.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
