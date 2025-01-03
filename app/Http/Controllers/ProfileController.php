<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function getProfile()
    {
        $user =  Auth::guard('api')->user(); 
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user' => $user], 200);
    }

    public function updateProfile(Request $request)
    {
        $user =  Auth::guard('api')->user(); 

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'school' => 'required|string|max:255',

            // 'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        // $user->email = $request->email;
        $user->class=$request->class;
        $user->country=$request->country;
        $user->address=$request->address;
        $user->school=$request->school;

        $user->save();
        
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }
}
