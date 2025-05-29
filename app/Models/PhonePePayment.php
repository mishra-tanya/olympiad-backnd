<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhonePePayment extends Model
{
    use HasFactory;

    protected $table = 'phonepepayments';

    protected $fillable = [
        'user_id',
        'payment_type',
        'transactionId',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}