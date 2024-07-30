<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'favorite',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function track():BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

}
