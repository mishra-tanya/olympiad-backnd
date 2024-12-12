<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goals;
use App\Models\Tests;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
     public function getGoals($className){
        $classNameNew = "class_" . $className;
        $goals = Goals::where('class_name', $classNameNew)->get();

        if ($goals->isEmpty()) {
            return response()->json(['message' => 'No goals found for this class.'], 404);
        }

        $leaderboard = DB::table('results')
        ->join('users', 'results.user_id', '=', 'users.id')  
        ->select('results.user_id', 'users.name', 'users.email','users.school', DB::raw('SUM(results.score) as total_score'))  
        ->where('results.class_id', $className)
        ->groupBy('results.user_id', 'users.name', 'users.email','users.school') 
        ->orderByDesc('total_score')
        ->get();
    
    // dd($leaderboard, $goals);
    
    $rank = 1;
        $leaderboard = $leaderboard->map(function ($user) use (&$rank) {
            $user->rank = $rank++;
            return $user;
        });

        return response()->json([
            'data' => [
                'goal' => $goals,
                'leaderboard' => $leaderboard
            ]
        ]);
        // return response()->json($goals);
     }

     public function getTests($className, $goal)
     {
         $classNameNew = "class_" . $className;
     
         $tests = Tests::where('class_id', $classNameNew)
             ->where('goal_id', $goal)
             ->get();
     
         if ($tests->isEmpty()) {
             return response()->json(['message' => 'No tests found for this goal.'], 404);
         }
         $leaderboard = DB::table('results')
         ->join('users', 'results.user_id', '=', 'users.id')  
         ->select('results.user_id', 'users.name', 'users.email','users.school', DB::raw('SUM(results.score) as total_score'))  
         ->where('results.goal_id', $goal)
         ->where('results.class_id', $className)
         ->groupBy('results.user_id', 'users.name', 'users.email','users.school') 
         ->orderByDesc('total_score')
         ->get();
     
    //  dd($leaderboard, $tests);
     
     $rank = 1;
         $leaderboard = $leaderboard->map(function ($user) use (&$rank) {
             $user->rank = $rank++;
             return $user;
         });
     
         return response()->json([
            'data' => [
                'tests' => $tests,
                'leaderboard' => $leaderboard
            ]
        ]);
     }
     
}
