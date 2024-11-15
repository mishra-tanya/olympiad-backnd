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
}
