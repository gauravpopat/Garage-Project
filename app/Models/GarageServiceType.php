<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarageServiceType extends Model
{
    use HasFactory;
    protected $fillable = ['garage_id', 'service_type_id'];
}
