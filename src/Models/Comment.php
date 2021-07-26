<?php

namespace Vongola\Imgur\Models;

use Jenssegers\Model\Model;

class Comment extends Model
{
    protected $fillable = [
        'id',
        'image_id',
        'comment',
        'author',
        'author_id',
        'on_album',
        'album_cover',
        'ups',
        'downs',
        'points',
        'datetime',
        'parent_id',
        'deleted',
        'vote',
    ];
}
