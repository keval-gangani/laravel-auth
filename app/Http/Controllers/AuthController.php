<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input(key:'first_name'),
            'last_name' => $request->input(key:'last_name'),
            'email' => $request->input(key:'email'),
            'password' => Hash::make($request->input(key:'password')),
        ]);

        return response($user, status: Response::HTTP_CREATED);
    }

    public function token(Request $request) {
        if(!Auth::attempt($request->only('email', 'password')))
        {
            return \response([
                'error' => 'Invalid Credentials!'
            ], Response::HTTP_UNAUTHORISED);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        return \response([
            'jwt' => $token
        ]);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password')))
        {
            return \response([
                'error' => 'Invalid Credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return \response([
            'jwt' => $token
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        return \response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

}
