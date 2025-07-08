<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'class'=>$request->className,
            'school'=>$request->school,
            'country'=>$request->country,
            'address'=>$request->address,
            'password' => Hash::make($request->password),
        ]);

        // Mail::to($user->email)->queue(new WelcomeMail($user));
        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('api')->user();
            $token = $user->createToken('LocalAppName')->plainTextToken;
            $role = $user->role;
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user_id' => $user->id,
                'role' => $role,
            ]);
        }
    else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });
    
            return response()->json(['message' => 'Logged out successfully'], 200);
        }
    
        return response()->json(['message' => 'User is not authenticated'], 401);
    }

  
}
