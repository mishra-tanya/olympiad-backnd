<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achiever extends Model
{
    protected $fillable = [
        'week_ending',
        'student_name',
        'student_school',
        'student_grade',
        'school_name',
        'school_location',
        'school_logo',
    ];

}
