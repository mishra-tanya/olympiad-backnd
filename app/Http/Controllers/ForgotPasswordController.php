<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'A reset link has been sent to your email address.'], 200);
        } else {
            return response()->json(['message' => 'Failed to send reset link.'], 400);
        }
    }

    public function showResetForm($token = null)
    {
        return view('reset')->with(
            ['token' => $token, 'email' => request()->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return view('success')->with(
                ['message'=>'Password has been reset successfully.']
            );
            return response()->json([
                'message' => 'Password has been reset successfully.',
                'redirect' => route('login')
            ], 200);
        } else {
            return response()->json(['message' => 'Failed to reset password. Please try again.'], 400);
        }
    }
}
