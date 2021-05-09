<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChallengeUser extends Pivot
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    protected $table = 'challenge_user';

    protected $fillable = [
        'id',
        'user_id',
        'challenge_id',
        'created_at',
        'updated_at'
    ];
}
