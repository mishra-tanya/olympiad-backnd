<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Result;
use App\Models\Certificate;
use App\Models\Goals;
use App\Models\Tests;
use App\Models\TestQuestions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResultController extends Controller
{
    public function store(Request $request)
{
    $data = $request->all();
    
    $score = 0;
    foreach ($data['answers'] as $answer) {
        if ($answer['user_answer'] === $answer['correct_answer']) {
            $score++;
        }
    }

    $answersJson = json_encode($data['answers']);

    $result = Result::updateOrCreate(
        [
            'user_id' => $data['userId'],
            'goal_id' => $data['goalId'],
            'class_id' => $data['classId'],
            'test_id' => $data['testId']
        ],
        [
            'score' => $score,
            'answers' => $answersJson,
        ]
    );

    return response()->json(['message' => 'Result stored successfully', 'data' => $result], 201);
}

    
//         $result = Result::create([
//             'user_id' => $data['userId'],  
//             'goal_id' => $data['goalId'],  
//             'class_id' => $data['classId'],  
//             'test_id' => $data['testId'],  
//             'score' => $score,
//             'answers' => $answersJson,  
//         ]);
    
         
    

    public function getResultsByUser($userId,$classId,$goalId,$testId)
    {
        $result = Result::where('user_id', $userId)
        ->where('class_id',$classId)
        ->where('goal_id',$goalId)
        ->where('test_id',$testId)
        ->first();;
        if (!$result) {
            return response()->json(['error' => 'Result not found'], 404);
        }
        $goal=Goals::where('id',$goalId)->first();
        $test=Tests::where('id',$testId)->first();

        // dd($result->answers);
        $answers = json_decode($result->answers, true);
        $score = 0;
        $questionIds = array_column($answers, 'question_id');
        $questions = TestQuestions::whereIn('id', $questionIds)
            ->get(['id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'reason']);
    
        $detailedResults = [];
        foreach ($questions as $question) {
            $userAnswer = collect($answers)->firstWhere('question_id', $question->id)['user_answer'] ?? null;
            
            $isCorrect = $userAnswer === $question->correct_answer;
            if ($isCorrect) {
                $score++;
            }
    
            $detailedResults[] = [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                ],
                'correct_answer' => $question->correct_answer,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'reason' => $question->reason,
            ];
        }

        return response()->json([
            'score' => $score,
            'total_questions' => count($questions),
            'detailed_results' => $detailedResults,
            'goalN'=>$goal,
            'testN'=>$test,
        ]);    
    }


public function generateCertificate(Request $request)
{
    $user=Auth::guard('api')->user();
    $userId = $user->id; 
    $username = ucwords($user->name);
    $userSchool = ucwords($user->school);
  
    // class_4-5
    // dd($username, $userSchool,$class);
    $type = $request->input('type'); 
    $classGr=$request->input('classGr');

    $certificate = Certificate::where('user_id', $userId)->where('certificate_type', $type)
    ->where('certificate_content',$classGr)
    ->first();

    if ($certificate) {
        return response()->json([
            'certificateNumber' => $certificate->certificate_id,
            'existing' => true,
            'date' => $certificate->created_at->format('Y-m-d'),
            'certificateId' => $certificate->id,
            'userId' => $certificate->user_id,
            'certificate_type' => $certificate->certificate_type,
            'username'=>$username,
            'userSchool'=>$userSchool,
            'classGroup'=>$certificate->certificate_content,
        ]);
    } else {
        $certificateNumber = 'CERT-' . strtoupper(uniqid()); 
        $certificate = Certificate::create([
            'user_id' => $userId,
            'certificate_type' => $type,
            'certificate_id' => $certificateNumber,
            'certificate_content'=>$classGr
        ]);

        return response()->json([
           'certificateNumber' => $certificate->certificate_id,
            'existing' => true,
            'date' => $certificate->created_at->format('Y-m-d'),
            'certificateId' => $certificate->id,
            'userId' => $certificate->user_id,
            'certificate_type' => $certificate->certificate_type,
            'username'=>$username,
            'userSchool'=>$userSchool,
            'classGroup'=>$certificate->certificate_content,

        ]);
    }

}

public function show($certificate_id)
{
    $certificate = Certificate::where('certificate_id', $certificate_id)->first();

    if (!$certificate) {
        abort(404, 'Certificate not found');
    }

    return view('certificate.show', ['certificate' => $certificate]);
}

}
