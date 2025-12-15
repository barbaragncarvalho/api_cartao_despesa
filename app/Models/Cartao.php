<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cartao extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'data_validade',
        'cvv',
        'saldo',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function despesas()
    {
        return $this->hasMany(Despesa::class);
    }
}
