<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarServicing extends Model
{
    use HasFactory;
    protected $fillable = ['garage_id', 'car_id', 'service_id', 'status'];
}
