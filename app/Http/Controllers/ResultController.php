<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Result;
use App\Models\Certificate;
use App\Models\TestQuestions;

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
        ]);    
    }


public function generateCertificate(Request $request)
{
    $certificateId = Str::random(10); 

    $certificate = Certificate::create([
        'certificate_id' => $certificateId,
        'user_id' => auth()->id(), 
        'certificate_content' => 'This is the certificate content', 
    ]);

    return redirect()->route('certificate.show', ['certificate_id' => $certificateId]);
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
