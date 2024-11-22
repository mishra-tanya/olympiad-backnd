<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Contact;

class ContactController extends Controller
{
    public function contactMessages(Request $request){
        // dd($request->input('name'));
        $name = $request->input('name');
        $contact_no = $request->input('contact_no');
        $email = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        Contact::create([
            'name' => $name,
            'contact_no' => $contact_no,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ]);

        return response()->json(['message' => 'Your message has been sent successfully!']);
   
    }
}
