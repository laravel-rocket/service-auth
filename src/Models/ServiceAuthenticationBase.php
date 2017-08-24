<?php
namespace LaravelRocket\ServiceAuthentication;

use LaravelRocket\Foundation\Models\Base;

/**
 * App\Models\ServiceAuthenticationBase.
 *
 * @mixin \Eloquent
 */
class ServiceAuthenticationBase extends Base
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'service',
        'service_id',
        'image_url',
        'service_token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [];
}
