<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackgroundCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'request_template_id',
        'amiqus_record_id',
        'amiqus_client_id',
        'status',
        'perform_url',
        'response_data',
        'expires_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_data' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the candidate that owns the background check.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the request template that was used for this background check.
     */
    public function requestTemplate()
    {
        return $this->belongsTo(RequestTemplate::class);
    }

    /**
     * Get the Amiqus record URL.
     *
     * @return string|null
     */
    public function getAmiqusRecordUrl()
    {
        if (!$this->amiqus_client_id || !$this->amiqus_record_id) {
            return null;
        }

        return config('amiqus.auth_url') . '/clients/' . $this->amiqus_client_id . '/records/' . $this->amiqus_record_id;
    }
}
