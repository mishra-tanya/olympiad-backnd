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

        return response()->json($goals);
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
         ->select('user_id', DB::raw('SUM(score) as total_score'))
         ->where('goal_id', $goal)
         ->where('class_id', $className)
         ->groupBy('user_id')
         ->orderByDesc('total_score')
         ->get();
     
    //  dd($leaderboard,$tests);
     
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
