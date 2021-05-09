<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeFile extends Model
{
    use HasFactory;

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function validation()
    {
        return $this->belongsTo(Validation::class, 'validation_id');
    }

    protected $table = 'challenges_files';

    protected $fillable = [
        'id',
        'link',
        'type',
        'width',
        'user_fileName',
        'user_id',
        'validation_id',
        'challenge_id',
        'created_at',
        'updated_at'
    ];
}
