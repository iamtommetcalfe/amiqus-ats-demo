<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmiqusOAuthAccessToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'amiqus_oauth_access_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'access_token',
        'token_type',
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
     * Get the client that owns the access token.
     */
    public function client()
    {
        return $this->belongsTo(AmiqusOAuthClient::class, 'client_id');
    }

    /**
     * Get the refresh token associated with the access token.
     */
    public function refreshToken()
    {
        return $this->hasOne(AmiqusOAuthRefreshToken::class, 'access_token_id');
    }
}
