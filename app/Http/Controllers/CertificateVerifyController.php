<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CertificateVerifyController extends Controller
{
   public function verifyCertificate($certificateId){
        // $certificate=$request->input('certificationId');
        $certification=Certificate::where('certificate_id',$certificateId)
        ->first();

        $certificationDate = $certification->created_at; 
        $formattedDate = Carbon::parse($certificationDate)->format('F d, Y');
    
        if ($certification) {
            return response()->json([
                'verified' => true,
                'name' => ucwords($certification->user->name),
                'school' => ucwords($certification->user->school),
                'date' => $formattedDate,
                'classGroup' => $certification->certificate_content,
            ]);
        } else {
            return response()->json(['verified' => false], 404);
        }
   }

   public function getAllCertificates(){
        $user=Auth::guard('api')->user();
        $userId=$user->id;
        $userName=ucwords($user->name);

        $certificate=Certificate::where('user_id',$userId)
        ->get();
        return response()->json([
            'userName' => $userName,
            'certificate' => $certificate
        ]);
   }

}
