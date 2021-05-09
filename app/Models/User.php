<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class)
            ->using(ChallengeUser::class)
            ->withPivot("id")
            ->withTimestamps();
    }

    public function createdChallenges()
    {
        return $this->hasMany(Challenge::class, 'user_id');
    }

    public function createdSessions()
    {
        return $this->hasMany(Session::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function validations()
    {
        return $this->hasMany(Validation::class, 'user_id');
    }

    public function scores()
    {
        return $this->hasMany(UserScore::class, 'user_id');
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'pseudo',
        'email',
        'password',
        'birthday',
        'phone',
        'auth_level',
        'address',
        'postal_code',
        'city',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
