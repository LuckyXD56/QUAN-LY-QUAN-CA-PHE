<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['order_session_id', 'user_id', 'total_amount', 'discount', 'final_amount', 'payment_method', 'status'];

    public function session()
    {
        return $this->belongsTo(OrderSession::class, 'order_session_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
