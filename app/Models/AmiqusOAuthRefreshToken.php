<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmiqusOAuthRefreshToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'amiqus_oauth_refresh_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access_token_id',
        'refresh_token',
        'expires_in',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the access token that owns the refresh token.
     */
    public function accessToken()
    {
        return $this->belongsTo(AmiqusOAuthAccessToken::class, 'access_token_id');
    }
}
