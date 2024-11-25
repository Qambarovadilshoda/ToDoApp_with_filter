<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'user' => $user
        ], 201);
    }
    public function login(LoginRequest $request){
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'User not found or password is incorrect'
            ], 404);
        }
        
        $token = $user->createToken('login')->plainTextToken;
        return response()->json([
            'message' => 'User logged successfully',
            'token' => $token
        ]);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'User logged out successfully'
        ], 204);
    }
}
