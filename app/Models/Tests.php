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
}
