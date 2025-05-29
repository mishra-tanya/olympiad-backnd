<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Result;
use App\Models\Payment;

class CertificateController extends Controller
{

    public function uploadCertificate(Request $request)
    {
        if (!$request->hasFile('certificate') || !$request->hasFile('preview_image')) {
            return response()->json(['error' => 'Certificate or preview image file missing'], 400);
        }

        $certFile = $request->file('certificate');
        $previewFile = $request->file('preview_image');

        $path = public_path('certificates/');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $certFilename = $certFile->getClientOriginalName();
        $previewFilename = pathinfo($certFilename, PATHINFO_FILENAME) . '.png';

        $certFile->move($path, $certFilename);
        $previewFile->move($path, $previewFilename);

        $certificateUrl = url('certificates/' . $certFilename);
        $previewUrl = url('certificates/' . $previewFilename);

        return response()->json([
            'message' => 'Certificate and preview image uploaded successfully',
            'certificateUrl' => $certificateUrl,
            'previewUrl' => $previewUrl,
        ]);
    }


    public function showCertificate($filename)
    {
        $path = public_path('certificates/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function checkClassCompletion(Request $request)
    {
        $user = Auth::user();
        $classId = $request->query('classId');

        $completedTestsCount = Result::where('user_id', $user->id)
            ->where('class_id', $classId)
            ->count();

        return response()->json([
            'completed' => $completedTestsCount >= 51,
        ]);
    }

    public function checkGoalCompletion(Request $request)
    {
        $user = Auth::user();
        $classId = $request->query('classId');
        $goalId = $request->query('goalId');

        $completedTestsCount = Result::where('user_id', $user->id)
            ->where('class_id', $classId)
            ->where('goal_id', $goalId)
            ->count();

        return response()->json([
            'completed' => $completedTestsCount >= 3,
        ]);
    }

    public function checkAllGoalCompletion(Request $request)
{
    $user = Auth::user();
    $classId = $request->query('classId');

    $completedGoals = Result::where('user_id', $user->id)
        ->where('class_id', $classId)
        ->select('goal_id')
        ->groupBy('goal_id')
        ->havingRaw('COUNT(*) >= 3')
        ->pluck('goal_id')
        ->map(fn($goalId) => (int) $goalId)  
        ->sort()
        ->values();

    return response()->json([
        'goals' => $completedGoals, 
    ]);
}


    public function checkClassPayment(Request $request)    {
        $user = auth()->user();
        $classId = $request->query('classId');
        $payment_type = "Certificate For Class ".$classId;
        $hasPaid = Payment::where('user_id', $user->id)
                        ->where('payment_type',$payment_type)
                        ->where('status', 'completed')
                        ->exists();
        return response()->json(['paid' => $hasPaid]);
    }


}
