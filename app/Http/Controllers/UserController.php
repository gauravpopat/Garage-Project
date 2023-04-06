<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ListingApiTrait;

class UserController extends Controller
{
    use ListingApiTrait;

    public function profile()
    {
        $user = auth()->user();
        return ok('User Detail', $user);
    }

    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Garage::query();
        $searchable_fields = ['city_id', 'state_id', 'country_id', 'name', 'address1', 'address2', 'zipcode'];
        $data = $this->filterSearchPagination($query, $searchable_fields);
        return ok('Garage List', [
            'garages'   =>  $data['query']->get(),
            'count'     =>  $data['count']
        ]);
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|min:8|max:18',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = auth()->user();
        if (password_verify($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
        }
        return error('Old Password Not Matched');
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();;
        return ok('Logout Successfully');
    }
}
