<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use App\Traits\ListingApiTrait;

class CityController extends Controller
{
    use ListingApiTrait;
    public function list($id)
    {
        $this->ListingValidation();
        $query = City::query();
        $searchable_fields = ['name'];
        $data = $this->filterSearchPagination($query, $searchable_fields);
        return ok('City List', [
            'cities'    =>  $data['query']->get(),
            'count'     =>  $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|max:50|unique:cities,name',
            'state_id'      => 'required|exists:states,id'
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $city = City::create($request->only('name', 'state_id'));
        return ok('City Added.', $city);
    }

    public function update($id, Request $request)
    {
        $city = City::findOrFail($id);

        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|unique:cities,name,' . $city->id,
            'state_id'      => 'required|exists:states,id'
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');
        $city->update($request->only('name', 'state_id'));
        return ok('City updated successfully.', $city);
    }

    public function delete($id)
    {
        $city = City::findOrFail($id);
        $city->garages()->delete();
        $city->delete();
        return ok('City Deleted Successfully');
    }

    public function show($id)
    {
        $city = City::findOrFail($id);
        return ok('City Detail', $city);
    }
}
