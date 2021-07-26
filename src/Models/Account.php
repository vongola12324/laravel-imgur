<?php

namespace Vongola\Imgur\Models;

use DateTime;
use Illuminate\Support\Carbon;
use Jenssegers\Model\Model;

/**
 * Class Account
 * @package Vongola\Imgur\Models
 * @property int id
 * @property string url
 * @property string|null avatar
 * @property string|null bio
 * @property float reputation
 * @property int|datetime|Carbon created
 * @property bool|int pro_expiration
 * @property array user_follow
 * @property-read bool|datetime|Carbon expired
 */
class Account extends Model
{
    protected $fillable = [
        'id',
        'url',
        'avatar',
        'bio',
        'reputation',
        'reputation_name',
        'created',
        'pro_expiration',
        'user_follow'
    ];

    protected $casts = [
        'id'              => 'integer',
        'url'             => 'string',
        'avatar'          => 'string',
        'bio'             => 'string',
        'reputation'      => 'float',
        'reputation_name' => 'string',
        'created'         => 'datetime',
        'user_follow'     => 'array'
    ];

    public function getExpiredAttribute()
    {
        if ($this->pro_expiration === false) {
            return false;
        } else {
            return Carbon::createFromTime($this->pro_expiration);
        }
    }
}
