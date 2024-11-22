<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table="results";
    protected $fillable = [
        'user_id',
        'goal_id',
        'class_id',
        'test_id',
        'score',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array', 
    ];
}
