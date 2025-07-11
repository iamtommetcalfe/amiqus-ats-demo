<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amiqus_id',
        'name',
        'description',
        'presets',
        'is_enabled',
        'amiqus_created_at',
        'amiqus_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'presets' => 'array',
        'is_enabled' => 'boolean',
        'amiqus_created_at' => 'datetime',
        'amiqus_updated_at' => 'datetime',
    ];
}
