<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSession extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'session_token', 'status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
