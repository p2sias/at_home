<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFile extends Model
{
    use HasFactory;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    protected $fillable = [
        'link',
        'type',
        'user_id',
        'post_id',
        'created_at',
        'updated_at'
    ];
}
