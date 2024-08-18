<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'spotify_track_id',
        'track_name',
        'artist_name',
        'album_name',
        'album_image_url',
        'uri',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
