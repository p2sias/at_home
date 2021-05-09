<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScores extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    protected $table = 'users_sessions_scores';

    protected $fillable = [
        'id',
        'user_id',
        'session_id',
        'score',
    ];

    public $timestamps = false;
}
