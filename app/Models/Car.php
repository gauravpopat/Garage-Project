<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $fillable = ['owner_id', 'company_name', 'model_name', 'manufacturing_year'];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services()
    {
        return $this->hasMany(CarServicing::class, 'car_id');
    }

    public function carServicings()
    {
        return $this->belongsToMany(ServiceType::class,'car_servicings','car_id','service_id');
    }

}
