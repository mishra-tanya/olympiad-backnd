<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Result;
use App\Models\Payment;

class CertificateController extends Controller
{
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
