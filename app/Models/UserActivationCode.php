<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserActivationCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'email', 'activation_code', 'verification_code', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
