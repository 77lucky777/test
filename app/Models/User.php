<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'balance'
    ];

    public $timestamps = false;

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class,
            'sender_id'
        );
    }
    
}
