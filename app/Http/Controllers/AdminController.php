<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Certificate;
use App\Models\Result;
use App\Models\Goals;
use App\Models\Tests;
use App\Models\TestQuestions;
use App\Models\Contact;
use Carbon\Carbon;


class AdminController extends Controller
{
    //users
    public function getUsersWithUserRole()
    {
        $users = User::where('role', 'user')
        ->orderBy('created_at', 'desc')->get();

        return response()->json([
            'user'=>$users
        ]);
    }

    // track
    public function getUserRegistrations()
    {
        $data = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

        return response()->json($data);
    }
    //certiifcates
    public function getAllCertificate(){
        $certificates = Certificate::with(['user:id,name,email'])
        ->orderBy('created_at', 'desc')
        ->get();
    
        return response()->json([
            'certificates' => $certificates
        ]);
    }

    // results for particular users 
    public function getResults($userId){
        $result=Result::with(['test:id,test_name','goal:id,goal_name'])
        ->where('user_id',$userId)
        ->orderBy('created_at', 'desc')->get();

        return response()->json([
            'result'=>$result
        ]);
    }

      // certificates for particular users 
      public function getCertificateUser($userId){
        $userCertificates=Certificate::where('user_id',$userId)
        ->with(['user:id,name,email'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'userCertificates'=>$userCertificates
        ]);
    }

    // goals 17 * 4 = 68
    public function getGoals(){
        $goal = Goals::paginate(10);

        return response()->json([
            'goal'=>$goal
        ]);
    }

    // get Tests 17 * 4 * 10 =  680
    public function getTests(){
        $tests=Tests::with('goal:id,goal_name')->paginate(10);

        return response()->json([
            'test'=>$tests
        ]);
    }

     // get Tests Questions 17 * 4 * 10 * 10 =  6800
     public function getTestQuestions()
     {
         $testQuestions = TestQuestions::with('goal:id,goal_name', 'test:id,test_name')->paginate(10);
     
         $testQuestions = $testQuestions->map(function ($question) {
             return [
                 'id' => $question->id,
                 'class_id' => $question->class_id,
                 'goal_name' => $question->goal->goal_name, 
                 'test_name' => $question->test->test_name, 
                 'question' => $question->question,
                 'option_a' => $question->option_a,
                 'option_b' => $question->option_b,
                 'option_c' => $question->option_c,
                 'option_d' => $question->option_d,
                 'reason' => $question->reason,
                 'correct_answer' => $question->correct_answer,
             ];
         });
     
         return response()->json([
             'testQuestions' => $testQuestions
         ]);
     }

    //contaxt msgs
    public function contact(){
        $contact=Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'contact'=>$contact
        ]);
    }

    //  detaisl for quicker acecss
     public function dashboard(){
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $userCount = User::where('role', 'user')->count();
        $totalTestsGiven = Result::whereDate('created_at', Carbon::today())->count();
        $newContactMessages = Contact::whereDate('created_at', Carbon::today())->count();
        $totalQuestions = TestQuestions::count();
        $totalGoals = Goals::count();
        $totalTests = Tests::count();
        $totalCertifications = Certificate::count();
        $newCertificationsToday = Certificate::whereDate('created_at', Carbon::today())->count();

        return response()->json([
            'newUsersToday' => $newUsersToday,
            'userCount' => $userCount,
            'totalTestsGiven' => $totalTestsGiven,
            'newContactMessages' => $newContactMessages,
            'totalQuestions' => $totalQuestions,
            'totalGoals' => $totalGoals,
            'totalTests' => $totalTests,
            'totalCertifications' => $totalCertifications,
            'newCertificationsToday' => $newCertificationsToday,
        ]);

     }
    

}
