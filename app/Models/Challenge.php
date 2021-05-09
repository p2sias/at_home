<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(ChallengeUser::class)
            ->withPivot("id")
            ->withTimestamps();
    }

    public function validations()
    {
        return $this->hasMany(Validation::class, 'challenge_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(ChallengeFile::class, 'challenge_id');
    }

    protected $fillable = [
        'id',
        'title',
        'description',
        'points',
        'main_picture',
        'session_id',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
