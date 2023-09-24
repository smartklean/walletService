<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'account_name', 'email', 'wallet_number', 'balance', 'lien', 'is_active',
      ];

    public function setEmailAttribute($value){
        return $this->attributes['email'] = trim(strtolower($value));
    }
}
