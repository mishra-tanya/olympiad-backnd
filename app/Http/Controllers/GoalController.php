<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goals;
use App\Models\Tests;

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

     public function getTests($className,$goal){
        $classNameNew="class_". $className;
        $tests=Tests::where('class_id',$classNameNew)
        ->where('goal_id',$goal)
        ->get();

        if($tests->isEmpty()){
            return response()->json(['message'=>'No tests found for this goal.'],404);
        }
        return response()->json($tests);
     }
}
