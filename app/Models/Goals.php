<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goals extends Model
{
    protected $table="goals";
    protected $fillable = [
        'class_name',
        'goal_name',
        'description',
    ];
    // for test questions 
    public function testQuestions()
    {
        return $this->hasMany(TestQuestions::class, 'goal_id');
    }
    public function test()
    {
        return $this->hasMany(Tests::class, 'goal_id');
    }
}
