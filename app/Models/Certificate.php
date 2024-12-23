<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table="certificate";
    protected $fillable = [
        'certificate_id',
        'user_id',
        'certificate_content',
        'certificate_type'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }
}
