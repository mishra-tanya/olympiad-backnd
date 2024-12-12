<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function getOverall(){
    $user = Auth::guard('api')->user(); 

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $results = $user->results;
        $totalScore = $results->sum('score'); 
        return response()->json([
            'user_id' => $user->id,
            'total_score' => $totalScore,
            'results' => $results,
        ]);
   }

   public function getByClass()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $classNames = ['4-5', '6-8', '9-10', '11-12'];
        $classResults = [];
    
        foreach ($classNames as $className) {
            $results = $user->results()->where('class_id', $className)->get();
            $classResults[$className] = [
                'total_score' => $results->sum('score'),
                'results' => $results,
            ];
        }
    
        return response()->json([
            'user_id' => $user->id,
            'class_results' => $classResults,
        ]);
    }

}
