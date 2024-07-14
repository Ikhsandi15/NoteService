<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $validateMsg = [
            'password.regex' => 'The :attribute must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character'
        ];

        $validation = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'password_confirmation' => 'required|same:password'
        ], $validateMsg);

        if ($validation->fails()) {
            return Helper::APIResponse("error validation", 422, $validation->errors(), null);
        }

        $password = Hash::make($req->password);

        User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => $password
        ]);

        return Helper::APIResponse("success", 200, null, null);
    }

    public function login(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse("error validation", 422, $validation->errors(), null);
        }

        $user = User::where('email', $req->email)->first();
        if (!$user) {
            return Helper::APIResponse('Phone Number or Password not match', 422, 'Phone Number or Password not match', null);
        }

        if (!Hash::check($req->password, $user->password)) {
            return Helper::APIResponse('Phone Number or Password not match', 422, 'Phone Number or Password not match', null);
        }

        $user['token'] = $user->createToken('user_token')->plainTextToken;
        return Helper::APIResponse("success", 200, null, $user);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return Helper::APIResponse('Logout Success', 200, null, null);
    }

    // hubungi lewat whatsapp admin?? Masih rancu sama ini
    public function forgotPassword()
    {
        $whatsapp_link = env("ADMIN_WHATSAPP");

        return redirect()->away($whatsapp_link);
    }
}
