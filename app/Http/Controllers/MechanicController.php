<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class MechanicController extends Controller
{
    public function profile()
    {
        $mechanic = auth()->user();
        return ok('Mechanic Profile',$mechanic->load('serviceTypes'));
    }

    // public function updateStatus(Request $request)
    // {
        
    // }
}
