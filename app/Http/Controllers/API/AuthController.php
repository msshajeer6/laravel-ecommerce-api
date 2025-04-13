<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            // 'role' => ['nullable', Rule::in(['admin', 'customer'])], // Validate role
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer', // Default to customer (We dont want anyone to register as admin)
            // 'role' => $request->role ?? 'customer', // Default to customer if not provided
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request){
       $credentials = $request->only(['email', 'password']);
       if(!auth()->attempt($credentials)){
           return response()->json(['message' => 'Invalid credentials'], 401);
       }
       $user = auth()->user();
       $token = $user->createToken('api-token')->plainTextToken;
       return response()->json([
           'message' => 'User logged in successfully',
           'user' => $user,
           'token' => $token,
       ], 200);
    }
}
