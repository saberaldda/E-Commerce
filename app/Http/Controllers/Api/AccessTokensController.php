<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;

class AccessTokensController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            'device_name' => ['nullable'],
            'abilities' => ['nullable'],
        ]);

        if (!$request->device_name) {
            $request->device_name = $request->header('User-Agent');
        }

        $user = User::where('email', $request->username)
            // ->orWhere('mobile', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Response::json([
                'message' => 'Invalid username or password',
            ], 401);
        }

        // Convert String To Array (,) The separeter
        $abilities = $request->input('abilities', ['*']);
        if ($abilities && is_string($abilities)) {
            $abilities = explode(',', $abilities);
        }
        $token = $user->createToken($request->device_name, $abilities);

        Log::info("User (".$user->name.") Logged IN By (api)", [
            'User Name'     => $user->name,
            'User Email'    => $user->email,
            'User Type'     => $user->type,
            'Logged At'     => now()->format('Y-m-d H:i:s'),
            'IP Address'    => $request->ip(),
            'over'          => 'api',
            'token'         => $token->plainTextToken,
            'device'        => $request->input('device_name'),
        ]);;

        return Response::json([
            'message'   => 'You Are Logged IN',
            'status'    => 201,
            'data'      => [
                'token' => $token->plainTextToken,
                'device_name'  => $token->accessToken->name,
                'user' => $user,
            ],
        ],201);
    }

    public function register(Request $request)
    {
        {
            $request->validate([
                'name'                  => 'required|max:255',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|min:8',
                'password_confirmation' => 'required|same:password',
            ]);
    
            $request->merge([
                'password'              => Hash::make($request->post('password')),
                'password_confirmation' => Hash::make($request->post('password_confirmation')),
                'type'                  => User::CLIENT,
            ]);
            
            $user = User::create($request->all());
            $user->refresh();
    
            return response()->json([
                'message'   => 'User Registered',
                'status'    => 201,
                'date'      => $user,
            ],201);
        }
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        Log::info("User (".$request->user()->name.") Logged OUT By (api)", [
            'User Name'     => $user->name,
            'User Email'    => $user->email,
            'User Type'     => $user->type,
            'Logged At'     => now()->format('Y-m-d H:i:s'),
            'IP Address'    => $request->ip(),
            'over'          => 'api',
            'device'        => $user->currentAccessToken()->name,
        ]);

        // Revoke (delete) all user tokens
        // $user->tokens()->delete();

        // Revoke current access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'message'   => 'You Are Logged OUT',
            'status'    => 200,
        ]);
    }
}
