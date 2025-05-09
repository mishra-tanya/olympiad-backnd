<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Result;
use Carbon\Carbon;


class AdminDashController extends Controller
{
    // data by geographic distribution
    public function getUserCountryDistribution()
    {
        $data = User::select('country', User::raw('COUNT(*) as count'))
            ->where('role', 'user')
            ->groupBy('country')
            ->get();
    
        return response()->json($data);
    }

    // get user by classes
    public function getUserClass()
    {
        $data = User::select('class', User::raw('COUNT(*) as count'))
            ->where('role', 'user')
            ->groupBy('class')
            ->get();
    
        return response()->json($data);
    }

    // get users completed all goals
    public function getUserCompletedGoals()
    {
        $data = User::select('class', DB::raw('COUNT(*) as count'))
        ->where('role', 'user')
        ->groupBy('class')
        ->havingRaw('COUNT(*) = 51') 
        ->get();

        return response()->json($data);
    }

    // get behavior
    public function behaviorDistribution()
    {
        $users = User::with(['results'])->get();

        $explorer = 0;
        $achiever = 0;
        $average = 0;
        $dormant = 0;

        foreach ($users as $user) {
            $results = $user->results;

            if ($results->isEmpty()) {
                $dormant++;
                continue;
            }

            $avgScore = $results->avg('score');
            $lastActive = $results->max('created_at');

            if (Carbon::parse($lastActive)->diffInDays(now()) > 30) {
                $dormant++;
            } elseif ($avgScore >= 8) {
                $achiever++;
            } elseif ($avgScore >= 4) {
                    $average++;
            } elseif ($avgScore >= 1) {
                $explorer++;
            } else {
                $dormant++;
            }
        }

        return response()->json([
            ['type' => 'Top Performer Score (Score>80)', 'count' => $achiever],
            ['type' => 'Average (Score>40)',  'count' => $average],
            ['type' => 'Learner', 'count' => $explorer],
            ['type' => 'Inactive Last 30 days',  'count' => $dormant],
        ]);
    }
    
    // top 10 performers
    public function topPerformers(){
        $topPerformers = User::withAvg('results', 'score') 
        ->having('results_avg_score', '>=', 8)         
        ->orderByDesc('results_avg_score')     
        ->take(10)                                  
        ->get(['id', 'name', 'email']);      

        return response()->json($topPerformers);
    }



    // tracking daily tests
    public function getTestsGiven(Request $request)
    {
        $days = $request->input('days', 30);
        
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $data = Result::select(Result::raw('DATE(created_at) as date'), Result::raw('COUNT(*) as count'))
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

}
