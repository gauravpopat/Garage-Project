<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{

    public function list()
    {
        $this->ListingValidation();
        $query = Country::query();
        $searchable_fields = ['name'];
        $data = $this->filterSearchPagination($query, $searchable_fields);
        return ok('Country List', [
            'countries'    =>  $data['query']->get(),
            'count'        =>  $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'name'  => 'required|max:50|unique:countries,name'
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $country = Country::create($request->only('name'));
        return ok('Country Added.', $country);
    }

    public function update($id, Request $request)
    {
        $country = Country::findOrFail($id);

        $validaiton = Validator::make($request->all(), [
            'name'  => 'required|unique:countries,name,' . $country->id
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $country->update($request->only('name'));

        return ok('Country updated successfully.', $country);
    }

    public function delete($id)
    {
        $country = Country::findOrFail($id);
        $country->states()->delete();
        $country->delete();

        return ok('Country Deleted Successfully');
    }

    public function show($id)
    {
        $country = Country::findOrFail($id)->load('states', 'cities');
        return ok('Country Detail', $country);
    }
}
