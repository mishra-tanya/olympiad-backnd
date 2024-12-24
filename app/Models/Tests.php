<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tests extends Model
{
    protected $table="goalstests";
    protected $fillable = [
        'class_id',
        'goal_id',
        'test_name',
        'description'
    ];
    // for test questions 
    public function testQuestions()
    {
        return $this->hasMany(TestQuestions::class, 'test_id');
    }
    
    public function goal()
    {
        return $this->belongsTo(Goals::class, 'goal_id','id');
    }
}
