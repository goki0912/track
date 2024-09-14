<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'track_id',
        'user_id',
        'theme_id',
        'likes_count',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function track():BelongsTo
    {
        return $this->belongsTo(Track::class);
    }
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_user_likes')->withTimestamps();
    }
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

}
