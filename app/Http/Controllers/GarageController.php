<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GarageController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|max:50',
            'address1'      => 'required',
            'zip_code'      => 'required',
            'city_id'       => 'required|exists:cities,id',
            'state_id'      => 'required|exists:states,id',
            'country_id'    => 'required|exists:countries,id',
            'owner_id'      => 'required|exists:users,id'
        ]);

        if($validation->fails())
            return error('Validation Error',$validation->errors(),'Validation');
        
        $garage = Garage::create($request->all());
        return ok('Garage Added.',$garage);
    }

    public function update($id,Request $request)
    {
        $validation = Validator::make($request->all(),[
            'name'          => 'required|max:50',
            'address1'      => 'required',
            'zip_code'      => 'required',
            'city_id'       => 'required|exists:cities,id',
            'state_id'      => 'required|exists:states,id',
            'country_id'    => 'required|exists:countries,id',
            'owner_id'      => 'required|exists:users,id'
        ]);

        if($validation->fails())
            return error('Validation Error',$validation->errors(),'validation');
    }
}
