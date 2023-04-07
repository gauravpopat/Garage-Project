<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Car;
use App\Models\CarServicing;
use App\Models\Garage;
use App\Traits\ListingApiTrait;

class CarServicingController extends Controller
{
    use ListingApiTrait;
    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Garage::query();
        $searchable_fields = ['city_id', 'state_id', 'country_id', 'name', 'address1', 'address2', 'zip_code'];
        $data = $this->filterSearchPagination($query, $searchable_fields);
        return ok('Garage List', [
            'garages'   =>  $data['query']->get(),
            'count'     =>  $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'garage_id'     => 'required|exists:garages,id',
            'car_id'        => 'required|exists:cars,id',
            'service_id'    => 'required|exists:service_types,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $car = Car::find($request->car_id);
        $carOwnerId = $car->user->id;

        if ($carOwnerId == auth()->user()->id) {
            CarServicing::create($request->all());
            return ok('Car Added For Service', $car);
        } else {
            return error('Car Not Found');
        }
    }
}
