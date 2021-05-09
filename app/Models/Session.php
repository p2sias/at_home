<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class, 'session_id');
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'session_id');
    }

    public function scores()
    {
        return $this->hasMany(UserScores::class, 'session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function validations()
    {
        return $this->hasManyThrough(Validation::class, Challenge::class, 'session_id', 'challenge_id', 'id', 'id');
    }


    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'max_participant',
        'status',
        'main_picture',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
