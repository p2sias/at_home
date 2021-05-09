<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(PostFile::class, 'post_id');
    }

    protected $fillable = [
        'title',
        'content',
        'short_desc',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
