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

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //users
    public function getUsersWithUserRole()
    {
        $users = User::where('role', 'user')
        ->orderBy('created_at', 'desc')->get();
        
        $result=[];
        
        foreach($users as $user){
            $userId=$user->id;

            $result[]=[
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'country' => $user->country,
                'address' => $user->address,
                'school' => $user->school,
                'class' => $user->class,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'goalCount' => $this->getCountGoal($userId),
                'testCount' => $this->getCountTest($userId),
            ];
        }

        return response()->json([
            'user'=>$result,
            // 'testCount'=>$testsCount,
            // 'goalCount'=>$goalsCount
        ]);
    }

    // count how many goals user has completed
    public function getCountGoal($userId)
    {
        $completedGoals = Result::select('goal_id', 'class_id')
            ->where('user_id', $userId)
            ->groupBy('goal_id', 'class_id')
            ->havingRaw('COUNT(*) >= 3')
            ->get();

    
        $results = $completedGoals->map(function ($item) {
            $goal = Goals::find($item->goal_id);
            return [
                'goal_id' => $item->goal_id,
                'goal_name' => $goal ? $goal->description : null,
                'class_id' => $item->class_id,
            ];
        });

        return $results;
    }
    
    // count how many tests user has done
    public function getCountTest($userId){
        $count= Result::where('user_id',$userId)->count();
        return $count;
     }
    // track
    public function getUserRegistrations(Request $request)
    {
        $days = $request->input('days', 7);  
    
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();
    
        $data = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();
    
        $dateRange = collect();
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->format('Y-m-d');
            $count = $data->firstWhere('date', $dateString)->count ?? 0;
            $dateRange->push(['date' => $dateString, 'count' => $count]);
            $currentDate->addDay();
        }
    
        return response()->json($dateRange);
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
         $totalQuestionsCount = TestQuestions::count();
     
         return response()->json([
             'testQuestions' => $testQuestions,
             'totalQuestionsCount'=>$totalQuestionsCount
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
