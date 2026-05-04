<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function cases()
    {
        return $this->hasMany(LegalCase::class, 'client_id');
    }
}