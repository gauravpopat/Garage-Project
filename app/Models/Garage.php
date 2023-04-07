<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garage extends Model
{
    use HasFactory;
    protected $fillable = ['city_id', 'state_id', 'country_id', 'owner_id', 'name', 'address1', 'address2', 'zip_code'];

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
