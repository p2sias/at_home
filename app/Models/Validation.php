<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function files()
    {
        return $this->hasMany(ChallengeFile::class, 'validation_id');
    }

    protected $fillable = [
        'comment',
        'status',
        'user_id',
        'challenge_id',
        'created_at',
        'updated_at'
    ];
}
