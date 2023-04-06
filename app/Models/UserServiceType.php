<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserServiceType extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'service_type_id'];
}
