<?php

namespace App\Http\Controllers;

use App\Models\CarServicing;
use App\Models\CarServicingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Car;
use App\Models\Garage;
use App\Notifications\UserNotifyByOwner;

class CarServicingJobController extends Controller
{

    public function list()
    {
        $carServicing = CarServicing::all();
        return ok('Car Servicing', $carServicing);
    }

    public function assign(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'car_servicing_id' => 'required|exists:car_servicings,id',
            'mechanic_id'      => 'required|exists:users,id',
            'service_type_id'  => 'required|exists:service_types,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::findOrFail($request->mechanic_id);
        if (!($user->type == 'Mechanic')) {
            return error('User ID :' . $request->mechanic_id . ' is not a mechanic');
        }

        CarServicingJob::create($request->only(['car_servicing_id', 'mechanic_id', 'service_type_id']) + [
            'status'        =>  'In-Progress',
            'description'   => 'Assigned'
        ]);

        return ok('Inserted Successfully');
    }

    public function review($id)
    {
        $carServicingJob = CarServicingJob::findOrFail($id);
        return ok('Car Servicing Job Detail', $carServicingJob);
    }

    public function delete($id)
    {
        $carServicingJob = CarServicingJob::findOrFail($id);
        $garage = Garage::find($carServicingJob->carServicings->garage_id);

        if (auth()->user()->id == $garage->owner_id) {
            $carServicingJob->delete();
            return ok('Job Deleted Successfully');
        }
        return error('No Access!');
    }
}
