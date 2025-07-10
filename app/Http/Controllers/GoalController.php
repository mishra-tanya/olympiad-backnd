<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goals;
use App\Models\Tests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

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
        ->select('results.user_id', 'users.name', 'users.email','users.school', 
        DB::raw('CAST(results.goal_id AS UNSIGNED) as goal_id'), DB::raw('SUM(results.score) as total_score'))  
        ->where('results.class_id', $className)
        ->groupBy('results.user_id', 'users.name', 'users.email','users.school', 'results.goal_id',) 
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
        $user = Auth::guard('sanctum')->user();
        $userId=$user->id;

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        
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

         $results = DB::table('results')
         ->where('results.goal_id', $goal)
         ->where('results.class_id', $className)
         ->where('results.user_id',$userId)
         ->get();
 
     $testsWithStatus = $tests->map(function ($test) use ($results, $goal, $className) {
         $test->status = $results->where('test_id', $test->id)->isNotEmpty() ? 'attempted' : 'not attempted';
         return $test;
     });
     
         return response()->json([
            'data' => [
                'tests' => $tests,
                'leaderboard' => $leaderboard,
                'testsWithStatus'=>$testsWithStatus
            ]
        ]);
     }
     
     public function goalName($goal) {
        $cacheKey = "goal_description_{$goal}";
        $description = Cache::remember($cacheKey, now()->addDays(30), function () use ($goal) {
            $goalData = Goals::where('id', $goal)->first();
            if (!$goalData) {
                return null;
            }
            return $goalData->description;
        });
        
        if (!$description) {
            return response()->json([
                "error" => "Goal not found"
            ], 404);
        }

        return response()->json([
            "goal" => $description
        ]);
    }
}
