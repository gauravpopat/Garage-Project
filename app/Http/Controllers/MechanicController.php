<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Car;
use App\Models\Garage;
use App\Notifications\WelcomeNotificationByMechanic;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\CarServicing;
use App\Models\CarServicingJob;
use App\Notifications\UserNotify;

class MechanicController extends Controller
{
    public function profile()
    {
        $mechanic = auth()->user();
        return ok('Mechanic Profile', $mechanic->load('serviceTypes', 'city'));
    }

    public function addCustomers(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name'            => 'required|max:50',
            'last_name'             => 'required|max:50',
            'email'                 => 'required|email|max:50|unique:users,email',
            'address1'              => 'required',
            'zip_code'              => 'required|digits:6',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'profile_picture'       => 'required|file',
            'city_id'               => 'required|exists:cities,id',
            'service_type_id'       => 'required_if:type,Mechanic|exists:service_types,id',
            'company_name'          => 'required',
            'model_name'            => 'required|unique:cars,model_name',
            'manufacturing_year'    => 'required|digits:4|before:' . (date('Y') + 1),
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        // store file in $profile
        $profile = $request->file('profile_picture');
        // generated new image name
        $profileName = 'profile' . time() . $profile->getClientOriginalExtension();

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'address1', 'address2', 'zip_code', 'phone', 'city_id', 'service_type_id']) + [
            'type'                      => 'Customer',
            'profile_picture'           => $profileName,
            'password'                  => Hash::make('12345678'),
            'billing_name'              => $request->first_name . " " . $request->last_name,
            'email_verification_code'   => Str::random(16)
        ]); // Created User

        if ($request->service_type_id) {
            $user->serviceTypes()->attach($request->service_type_id);
        }

        // Move company logo into storage/app/public/users/profile_pictures/{user_id}/...
        $path = storage_path('app/public/users/profile_pictures/') . $user->id;
        $profile->move($path, $profileName);


        Car::create($request->only(['company_name', 'model_name', 'manufacturing_year']) + [
            'owner_id'  => $user->id
        ]);

        // Welcome notification with email verification link.
        $user->notify(new WelcomeNotificationByMechanic($user));
        return ok('Insert Successfull.', $user);
    }

    public function registerGarage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'              => 'required|max:50',
            'address1'          => 'required',
            'address2'          => 'required',
            'zip_code'          => 'required',
            'city_id'           => 'required|exists:cities,id',
            // 'owner_id'          => 'required|exists:users,id',
            'service_type_id'   => 'required|array|exists:service_types,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = auth()->user();
        //find country and state based on city.
        $city       = City::where('id', $request->city_id)->first();
        $state      = $city->state;
        $country    = Country::findOrFail($state->country_id);

        $garage = Garage::create($request->only(['name', 'address1', 'address2', 'zip_code', 'city_id']) + [
            'state_id'      =>  $state->id,
            'country_id'    =>  $country->id,
            'owner_id'      =>  $user->id
        ]);

        $user->update([
            'garage_id' => $garage->id,
            'type'      => 'Owner'
        ]);

        $garage->serviceTypes()->attach($request->service_type_id); // Added Services In Garage.

        return ok('Garage Added.', $garage);
    }

    public function updateStatus($id, Request $request)
    {

        $carServicingJob = CarServicingJob::findOrFail($id);

        if ($carServicingJob->mechanic_id != auth()->user()->id) {
            return error('No Job Found');
        }

        $validation = Validator::make($request->all(), [
            'status'        => 'required|in:Pending,In-Progress,Complete',
            'description'   => 'required_if:status,Complete'
        ]);

        if ($validation->fails())
            return error('Validation', $validation->errors(), 'validation');

        $carServicingJob->update($request->only('status', 'description'));

        if ($carServicingJob->status == 'Complete') {
            $carServicing = CarServicing::where('id', $carServicingJob->car_servicing_id)->first();
            $car    =   Car::where('id', $carServicing->car_id)->first();
            $user   =   $car->user;
            $user->notify(new UserNotify($car));
        }

        return ok('Status Updated Successfully');
    }
}
