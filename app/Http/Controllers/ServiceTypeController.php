<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceTypeController extends Controller
{
    public function list($id)
    {
        $serviceType = ServiceType::find($id);
        return ok('Service Type', $serviceType);
    }

    public function create(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|max:50|unique:service_types,name',
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $serviceType = ServiceType::create($request->only('name'));
        return ok('Service Type Added.', $serviceType);
    }

    public function update($id, Request $request)
    {
        $serviceType = ServiceType::find($id);

        $validaiton = Validator::make($request->all(), [
            'name'          => 'required|unique:service_types,name,' . $serviceType->id,
        ]);

        if ($validaiton->fails())
            return error('Validation Error', $validaiton->errors(), 'Validation');

        $serviceType->update($request->only('name', 'state_id'));
        return ok('Service Type Updated Successfully.', $serviceType);
    }

    public function delete($id)
    {
        $serviceType = ServiceType::find($id);
        $serviceType->delete();
        return ok('Service Type Deleted Successfully');
    }

    public function show($id)
    {
        $serviceType = ServiceType::find($id);
        return ok('Service Type Detail', $serviceType);
    }
}