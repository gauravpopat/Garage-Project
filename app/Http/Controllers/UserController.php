<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ListingApiTrait;
use Illuminate\Console\View\Components\Confirm;

class UserController extends Controller
{
    use ListingApiTrait;

    public function profile()
    {
        $user = auth()->user()->load('cars', 'city');
        return ok('User Detail', $user);
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|min:8|max:18|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = auth()->user();

        if (password_verify($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
            return ok('Password Changed Successfully');
        } else {
            return error('Old Password Not Matched');
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return ok('You have been logged out.');
    }
}
