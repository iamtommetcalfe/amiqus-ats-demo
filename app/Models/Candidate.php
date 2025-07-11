<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter_path',
        'notes',
        'source',
        'current_company',
        'current_position',
        'amiqus_client_id',
    ];

    /**
     * Get the full name of the candidate.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the interviews for the candidate.
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the job postings that the candidate has applied to.
     */
    public function jobPostings()
    {
        return $this->belongsToMany(JobPosting::class, 'interviews')
            ->withPivot('interview_stage_id', 'scheduled_at', 'feedback', 'notes', 'status')
            ->withTimestamps();
    }

    /**
     * Check if the candidate is connected to an Amiqus client.
     *
     * @return bool
     */
    public function isConnectedToAmiqus()
    {
        return ! is_null($this->amiqus_client_id);
    }

    /**
     * Get the Amiqus client URL.
     *
     * @return string|null
     */
    public function getAmiqusClientUrl()
    {
        if (! $this->isConnectedToAmiqus()) {
            return null;
        }

        return config('amiqus.auth_url').'/clients/'.$this->amiqus_client_id;
    }

    /**
     * Get the background checks for the candidate.
     */
    public function backgroundChecks()
    {
        return $this->hasMany(BackgroundCheck::class);
    }
}
