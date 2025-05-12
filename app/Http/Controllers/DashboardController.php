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
   
       $classNames = ['4', '5', '6', '7','8','9','10'];
       $classResults = [];
   
       foreach ($classNames as $className) {
           $results = $user->results()
               ->where('class_id', $className)
               ->with(['goal', 'test']) 
               ->orderBy('created_at', 'desc')
               ->get();
   
           $classResults[$className] = [
               'total_score' => $results->sum('score'),
               'results' => $results->map(function ($result) {
                   return [
                       'id' => $result->id,
                       'user_id' => $result->user_id,
                       'class_id'=>$result->class_id,
                       'goal_id' => $result->goal_id,
                       'goal_name' => $result->goal ? $result->goal->description : null,  
                       'test_id' => $result->test_id,
                       'test_name' => $result->test ? $result->test->test_name : null,  
                       'score' => $result->score,
                       'answers' => json_decode($result->answers), 
                       'created_at' => $result->created_at,
                       'updated_at' => $result->updated_at,
                   ];
               }),
           ];
       }
   
       return response()->json([
           'user_id' => $user->id,
           'class_results' => $classResults,
       ]);
   }
   

}
