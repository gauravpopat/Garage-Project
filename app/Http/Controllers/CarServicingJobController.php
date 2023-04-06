<?php

namespace App\Http\Controllers;

use App\Models\CarServicing;
use App\Models\CarServicingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarServicingJobController extends Controller
{
    public function list()
    {
        $carServicing = CarServicing::all();
        return ok('Car Servicing',$carServicing);
    }

    public function assign(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'car_servicing_id' => 'required|exists:car_servicings,id',
            'mechanic_id'      => 'required|exists:mechanics,id',
            'service_type_id'  => 'required|exists:service_types,id'
        ]);

        if($validation->fails())
            return error('Validation Error',$validation->errors(),'Validation');
        
        CarServicingJob::create($request->only(['car_servicing_id','mechanic_id','service_type_id'])+[
            'status'    =>  'pending'
        ]);
    }

    public function update($id,Request $request)
    {
        $validation = Validator::make($request->all(),[
            'status'    => 'required|in:Pending,In-Progress,Complete'
        ]);

        if($validation->fails())
            return error('Validation',$validation->errors(),'validation');
        
        $carServicingJob = CarServicingJob::findOrFail($id);
        
        $carServicingJob->update($request->only('status'));
        return ok('Status Updated Successfully');
    }
}
