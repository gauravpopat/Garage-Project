<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Car;
use App\Models\CarServicing;
use App\Models\CarServicingJob;
use App\Models\Garage;
use App\Traits\ListingApiTrait;
use App\Models\User;

class CarServicingController extends Controller
{
    use ListingApiTrait;
    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Garage::query();
        $searchable_fields = ['city_id', 'state_id', 'country_id', 'name'];
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

        $car = Car::findOrFail($request->car_id);
        $carOwnerId = $car->user->id;


        if ($carOwnerId == auth()->user()->id) {
            CarServicing::create($request->only(['garage_id', 'car_id', 'service_id']));
            return ok('Car Added For Service', $car);
        } else {
            return error('Car Not Found');
        }
    }

    public function getHistory(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'car_id'    => 'required|exists:cars,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $carServicing = CarServicing::where('car_id', $request->car_id)->first();
        if ($carServicing) {
            return ok('History', $carServicing);
        }

        return error('No Any Services Found.');
    }


    public function getStatus(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'car_servicing_id'  => 'required|exists:car_servicings,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $carServicing = CarServicing::where('id', $request->car_servicing_id)->first();
        $car = Car::where('id', $carServicing->car_id)->where('owner_id', auth()->user()->id)->first();

        if ($car) {
            $carServicingJob = CarServicingJob::where('car_servicing_id', $carServicing->id)->first();
            $mechanic = User::where('id', $carServicingJob->mechanic_id)->first();
            return response()->json([
                'message'   => 'status',
                'mechanic'  => $mechanic,
                'status'    => $carServicingJob->status
            ]);
        }

        return error('No Record Found');
    }
}
