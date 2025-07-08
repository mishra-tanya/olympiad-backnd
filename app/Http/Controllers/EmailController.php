<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendBulkEmail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'emails' => 'required|array',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        foreach ($validated['emails'] as $i => $email) {
            SendBulkEmail::dispatch($email, $validated['message'], $validated['subject'])
                ->delay(now()->addSeconds($i * 20));
        }

        return response()->json(['status' => 'Emails queued with delay!']);
    }
}
