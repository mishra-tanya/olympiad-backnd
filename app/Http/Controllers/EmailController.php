<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class EmailController extends Controller
{

    public function send(Request $request)
    {
        $validated = $request->validate([
            'emails' => 'required|array',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        foreach ($validated['emails'] as $email) {
            Mail::raw($validated['message'], function ($mail) use ($email, $validated) {
                $mail->to($email)->subject($validated['subject']);
            });
        }

        return response()->json(['status' => 'Emails sent successfully!']);
    }

}
