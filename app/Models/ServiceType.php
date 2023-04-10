<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_type');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_type');
    }
}
