<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewStage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'is_default',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the interviews for this stage.
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Scope a query to only include default stages.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to order stages by their order value.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
