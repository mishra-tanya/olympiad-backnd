<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfController extends Controller
{
  public function getCsrfCookie(Request $request)
    {
        return response()->json(['message' => 'CSRF token set']);
    }
}
