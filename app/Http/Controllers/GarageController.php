<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GarageController extends Controller
{
    public function list()
    {
        $this->ListingValidation();
        $query = auth()->user()->garages;
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
            'name'              => 'required|max:50',
            'address1'          => 'required',
            'address2'          => 'required',
            'zip_code'          => 'required|digits:6',
            'city_id'           => 'required|exists:cities,id',
            'service_type_id'   => 'required|array|exists:service_types,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = auth()->user();

        //find country and state based on city.
        $city    = City::where('id', $request->city_id)->first();
        $state   = $city->state;
        $country = Country::findOrFail($state->country_id);

        $garage = Garage::create($request->only(['name', 'address1', 'address2', 'zip_code', 'city_id']) + [
            'state_id'      =>  $state->id,
            'country_id'    =>  $country->id,
            'owner_id'      =>  $user->id
        ]);

        $user->update([
            'garage_id' => $garage->id
        ]);

        $garage->serviceTypes()->attach($request->service_type_id); // Added Services In Garage.

        return ok('Garage Added.', $garage);
    }

    public function update($id, Request $request)
    {
        $garage = auth()->user()->garages()->findOrFail($id);

        $validation = Validator::make($request->all(), [
            'name'          => 'required|max:50',
            'address1'      => 'required',
            'address2'      => 'required',
            'zip_code'      => 'required',
            'city_id'       => 'required|exists:cities,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $city = City::where('id', $request->city_id)->first();
        $state = $city->state;
        $country = Country::findOrFail($state->country_id);

        $garage->update($request->only(['name', 'address1', 'address2', 'zip_code', 'city_id']) + [
            'state_id'      => $state->id,
            'country_id'    => $country->id,
            'owner_id'      => auth()->user()->id
        ]);

        return ok('Garage Updated Successfully');
    }

    public function delete($id)
    {
        $garage = auth()->user()->garages()->findOrFail($id);
        $garage->delete();
        return ok('Garage Deleted Successfully');
    }

    public function show($id)
    {
        $garage = auth()->user()->garages()->findOrFail($id);
        return ok('Garage Detail', $garage);
    }
}
