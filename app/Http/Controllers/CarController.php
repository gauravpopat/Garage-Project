<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarServicing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'company_name'          => 'required',
            'model_name'            => 'required',
            'manufacturing_year'    => 'required|digits:4|before:' . (date('Y') + 1),
            'garage_id'             => 'required|exists:garages,id',
            'service_id'            => 'required|exists:service_types'

        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $car = Car::create($request->only(['company_name', 'model_name', 'manufacturing_year']) + [
            'owner_id'  => auth()->user()->id,
        ]);

        CarServicing::create($request->only(['garage_id', 'service_id']) + [
            'car_id'    => $car->id,
            'status'    => 'Initiated',
        ]);

        return ok('Car Goes to the Service.', $car);
    }
}
