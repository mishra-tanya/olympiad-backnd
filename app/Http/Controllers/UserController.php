<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getEmails()
    {
        $emails = User::select('email')->get();
        return response()->json($emails);
    }

}
