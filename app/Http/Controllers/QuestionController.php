<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestQuestions;
use App\Models\Goals;
use App\Models\Tests;

class QuestionController extends Controller
{
        public function getQuestions($className, $goalName, $testName)
    {
        $classNameNew = "class_" . $className;
        $questions = TestQuestions::where('class_id', $classNameNew)
            ->where('goal_id', $goalName)
            ->where('test_id', $testName)
            ->get();

        $goal=Goals::where('id',$goalName)->get();
        $test=Tests::where('id',$testName)->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No questions found for this test.'], 404);
        }

        return response()->json(
            [
            'questions'=>$questions,
            'goal'=>$goal,
            'testName'=>$test
            ]
        );
    }

}
