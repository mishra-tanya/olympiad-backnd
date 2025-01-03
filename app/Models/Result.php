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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goal()
    {
        return $this->belongsTo(Goals::class);
    }

    public function test()
    {
        return $this->belongsTo(Tests::class);
    }
}
