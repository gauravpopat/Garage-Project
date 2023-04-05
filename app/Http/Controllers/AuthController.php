<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name'            => 'required|max:50',
            'last_name'             => 'required|max:50',
            'email'                 => 'required|email|max:50|unique:users,email',
            'type'                  => 'required|in:Customer,Mechanic',
            'address1'              => 'required',
            'zip_code'              => 'required|digits:6',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'profile_picture'       => 'required|file',
            'city_id'               => 'required|exists:cities,id',
            'garage_id'             => 'required|exists:garages,id',
            'service_type_id'       => 'required_if:type,Mechanic',
            'password'              => 'required|min:8|max:18',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'Validation');

        // store file in $profile
        $profile = $request->file('profile_picture');
        // generated new image name
        $profileName = 'profile' . time() . $profile->getClientOriginalExtension();

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'type', 'address1', 'address2', 'zip_code', 'phone', 'city_id', 'garage_id', 'service_type_id']) + [
            'profile_picture'           => $profileName,
            'password'                  => Hash::make($request->password),
            'billing_name'              => $request->first_name . " " . $request->last_name,
            'email_verification_code'   => Str::random(16)
        ]); // Created User

        // Move company logo into storage/app/public/users/profile_pictures/{user_id}/...
        $path = storage_path('app/public/users/profile_pictures/') . $user->id;
        $profile->move($path, $profileName);

        // Welcome notification with email verification link.
        $user->notify(new WelcomeNotification($user));
    }

    public function verifyEmail($email_verification_code)
    {
        $user = User::where('email_verification_code', $email_verification_code)->first();
        $user->update([
            'is_verified'               =>  true,
            'email_verification_code'   =>  null
        ]);
        return ok('Email Verification Successfull.');
    }


    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'     =>  'required|exists:users,email',
            'password'  =>  'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'Validation');

        if (Auth::attempt(['email'  =>  $request->email, 'password' => $request->password])) {
            $token = auth()->user()->createToken('Login Token')->accessToken;
            return ok('Login Successfull.', $token);
        } else {
            return error('Password Incorrect');
        }
    }
}
