<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestQuestions;
use App\Models\Goals;
use App\Models\Tests;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    public function getQuestions($className, $goalName, $testName)
    {
        $classNameNew = "class_" . $className;
        $cacheKey = "questions_{$classNameNew}_{$goalName}_{$testName}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return response()->json($cachedData);
        }
        $questions = TestQuestions::where('class_id', $classNameNew)
            ->where('goal_id', $goalName)
            ->where('test_id', $testName)
            ->get();

        $goal=Goals::where('id',$goalName)->get();
        $test=Tests::where('id',$testName)->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No questions found for this test.'], 404);
        }

        $response = [
            'questions' => $questions,
            'goal' => $goal,
            'testName' => $test
        ];

        Cache::put($cacheKey, $response, now()->addMinutes(60));

        return response()->json($response);
    }

}
