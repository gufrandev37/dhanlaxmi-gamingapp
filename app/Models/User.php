<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens, Notifiable;

    protected $fillable = [
        'cin',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ===============================
       RELATIONSHIPS
    =============================== */

    
    public function wallets()
{
    return $this->hasMany(Wallet::class);
}


   
    public function withdraws()
{
    return $this->hasMany(Withdraw::class);
}


    public function winnings()
    {
        return $this->hasMany(Winning::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function bankDetail()
{
    return $this->hasOne(BankDetail::class);
}

    public function games()
{
    return $this->hasMany(Game::class);
}

public function wallet()
{
    return $this->hasOne(Wallet::class);
}
}
