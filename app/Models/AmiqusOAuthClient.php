<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiqusOAuthClient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'amiqus_oauth_clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'redirect_uri',
        'scope',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the access tokens for the client.
     */
    public function accessTokens()
    {
        return $this->hasMany(AmiqusOAuthAccessToken::class, 'client_id');
    }
}
