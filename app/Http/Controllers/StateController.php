<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function list($id)
    {
        $state = State::find($id);
        return ok('State', $state);
    }

    public function create(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|max:50|unique:states,name',
            'country_id'    => 'required|exists:countries,id'
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $state = State::create($request->only('name', 'country_id'));
        return ok('State Added.', $state);
    }

    public function update($id, Request $request)
    {
        $state = State::find($id);

        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|unique:states,name,' . $state->id,
            'country_id'    => 'required|exists:countries,id'
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');
            
        $state->update($request->only('name', 'country_id'));
        return ok('State updated successfully.', $state);
    }

    public function delete($id)
    {
        $state = State::find($id);
        $state->delete();
        return ok('State Deleted Successfully');
    }

    public function show($id)
    {
        $state = State::find($id);
        return ok('State Detail', $state);
    }
}
