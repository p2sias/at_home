<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'user_id',
        'order',
        'created_at',
        'updated_at'
    ];
}
