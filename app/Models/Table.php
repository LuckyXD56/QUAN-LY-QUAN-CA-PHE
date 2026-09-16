<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'qr_token', 'is_active'];

    public function sessions()
    {
        return $this->hasMany(OrderSession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(OrderSession::class)->where('status', 'active')->latest();
    }
}
