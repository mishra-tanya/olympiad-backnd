<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestions extends Model
{
    protected $table="testsquestions";
    protected $fillable = [
        'class_id',
        'goal_id',
        'test_id',
        'question',
        'option_a', 'option_b', 'option_c', 'option_d', 'reason', 'correct_answer',
    ];
    // for test questions 
    public function goal()
    {
        return $this->belongsTo(Goals::class, 'goal_id','id');
    }

    public function test()
    {
        return $this->belongsTo(Tests::class, 'test_id', 'id');  
    }
}
