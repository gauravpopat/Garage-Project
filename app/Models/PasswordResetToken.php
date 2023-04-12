<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'email';
    protected $fillable = ['expiry_date','email','token','updated_at','created_at'];
}
