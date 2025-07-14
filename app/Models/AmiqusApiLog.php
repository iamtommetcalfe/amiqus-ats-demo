<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmiqusApiLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'method',
        'url',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'duration',
        'error',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
        'response_body' => 'array',
        'duration' => 'float',
    ];

    /**
     * Get the parent loggable model (e.g., candidate).
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
