<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
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
            'type'                  => 'in:Customer,Mechanic,Owner',
            'address1'              => 'required',
            'zip_code'              => 'required|digits:6',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'profile_picture'       => 'required|file',
            'city_id'               => 'required|exists:cities,id',
            'service_type_id'       => 'required_if:type,Mechanic|exists:service_types,id',
            'password'              => 'required|confirmed|min:8|max:18',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        // store file in $profile
        $profile = $request->file('profile_picture');
        // generated new image name
        $profileName = 'profile' . time() . $profile->getClientOriginalExtension();

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'type', 'address1', 'address2', 'zip_code', 'phone', 'city_id', 'service_type_id']) + [
            'profile_picture'           => $profileName,
            'password'                  => Hash::make($request->password),
            'billing_name'              => $request->first_name . " " . $request->last_name,
            'email_verification_code'   => Str::random(16)
        ]); // Created User

        if ($request->service_type_id) {
            $user->serviceTypes()->attach($request->service_type_id);
        }

        // Move company logo into storage/app/public/users/profile_pictures/{user_id}/...
        $path = storage_path('app/public/users/profile_pictures/') . $user->id;
        $profile->move($path, $profileName);

        // Welcome notification with email verification link.
        $user->notify(new WelcomeNotification($user));
        return ok('Insert Successfull.', $user);
    }

    public function verifyEmail($email_verification_code)
    {
        $user = User::where('email_verification_code', $email_verification_code)->firstOrFail();
        $user->update([
            'is_verified'               => true,
            'email_verification_code'   => null
        ]);
        return ok('Email Verification Successfull.');
    }

    public function resetPasswordLink(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::where('email', $request->email)->first();
        $token = Str::random(16);

        PasswordResetToken::create([
            'email'         => $user->email,
            'token'         => $token,
            'expiry_date'   => now()->addDays(2)
        ]);

        $user->notify(new ResetPasswordNotification($token));
        return ok('Mail Sent!');
    }

    public function resetPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:users,email|exists:password_reset_tokens,email',
            'token'                 => 'required|exists:password_reset_tokens,token',
            'password'              => 'required|min:8|max:18|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $passwordResetToken = PasswordResetToken::where('token', $request->token)->first();

        if ($passwordResetToken->expiry_date < now())
            return error('Token Expired');

        $user = User::where('email', $passwordResetToken->email)->first();
        $user->update([
            'password'  => Hash::make($request->password)
        ]);
        $passwordResetToken->delete();
        return ok('Password Changed Successfully');
    }


    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'     =>  'required|exists:users,email',
            'password'  =>  'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::where('email', $request->email)->first();

        if ($user->is_verified == true) {
            if (Auth::attempt(['email'  =>  $request->email, 'password' => $request->password])) {
                $token = $user->createToken('Login Token')->plainTextToken;
                return ok('Login Successfull.', $token);
            } else {
                return error('Password Incorrect');
            }
        } else {
            return error('Email not verified');
        }
    }
}
