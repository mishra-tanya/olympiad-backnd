<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function getEmails(Request $request) 
    {
        $now = Carbon::now();

        $query = User::select('id', 'name', 'email', 'school', 'role') 
            ->where('role', 'user') 
            ->withCount(['results as average_score' => function ($q) {
                $q->select(DB::raw('avg(score)'));
            }])
            ->withMax('results', 'created_at');

        if ($request->has('filter')) {
            $filter = $request->input('filter');

            if ($filter === 'top_performers') {
                $query->having('average_score', '>=', 10);
            } elseif ($filter === 'inactive_1w') {
                $query->whereDoesntHave('results', function ($q) use ($now) {
                    $q->where('created_at', '>=', $now->copy()->subWeek());
                });
            } elseif ($filter === 'inactive_2w') {
                $query->whereDoesntHave('results', function ($q) use ($now) {
                    $q->where('created_at', '>=', $now->copy()->subWeeks(2));
                });
            } elseif ($filter === 'inactive_1m') {
                $query->whereDoesntHave('results', function ($q) use ($now) {
                    $q->where('created_at', '>=', $now->copy()->subMonth());
                });
            } elseif ($filter === 'paid_users') {
                $query->whereHas('payments', function ($q) {
                    $q->where('status', 'completed');
                });
            }
        }

        $users = $query->get();

        return response()->json($users);
    }

}
