<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_session_id', 'user_id', 'status', 'notes'];

    public function session()
    {
        return $this->belongsTo(OrderSession::class, 'order_session_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
