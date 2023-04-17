<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Garage;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    use ListingApiTrait;
    public function list()
    {
        $this->ListingValidation();
        $query = Car::where('owner_id', auth()->user()->id);
        $searchable_fields = ['company_name', 'model_name', 'manufacturing_year'];
        $data = $this->filterSearchPagination($query, $searchable_fields);
        return ok('Car List', [
            'cars'      =>  $data['query']->get(),
            'count'     =>  $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'company_name'          => 'required',
            'model_name'            => 'required|unique:cars,model_name',
            'manufacturing_year'    => 'required|digits:4|before:' . (date('Y') + 1),
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $car = Car::create($request->only(['company_name', 'model_name', 'manufacturing_year']) + [
            'owner_id'  => auth()->user()->id,
        ]);

        return ok('Car Added Successfully', $car);
    }

    public function update($id, Request $request)
    {
        $car = Car::findOrFail($id);
        $carOwnerId = $car->user->id;

        if (($carOwnerId != auth()->user()->id)) {
            return error('Not your car.');
        }

        $validation = Validator::make($request->all(), [
            'company_name'          => 'required',
            'model_name'            => 'required',
            'manufacturing_year'    => 'required|digits:4|before:' . (date('Y') + 1),
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $car->update($request->all());
        return ok('Car Updated Successfully.');
    }

    public function show($id)
    {
        $car = Car::findOrFail($id);
        $carOwnerId = $car->user->id;

        if ($carOwnerId == auth()->user()->id) {
            return ok('Car Detail.', $car);
        } else {
            return error('Car Not Found');
        }
    }

    public function delete($id)
    {
        $car = Car::findOrFail($id);
        $carOwnerId = $car->user->id;

        if ($carOwnerId == auth()->user()->id) {
            $car->delete();
            return ok('Car Deleted Successfully');
        } else {
            return error('Car Not Found');
        }
    }
}
