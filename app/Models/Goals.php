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
}
