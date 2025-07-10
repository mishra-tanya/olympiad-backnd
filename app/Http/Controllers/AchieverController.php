<?php

namespace App\Http\Controllers;

use App\Models\Achiever;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AchieverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievers = Cache::remember('achievers_list', now()->addDays(7), function () {
            return Achiever::orderBy('week_ending', 'desc')->get();
        });

        return response()->json($achievers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'week_ending' => 'required|date',
            'student_name' => 'required|string',
            'student_school' => 'required|string',
            'student_grade' => 'required|string',
            'school_name' => 'required|string',
            'school_location' => 'required|string',
            'school_logo' => 'nullable|image|max:2048', 
        ]);

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('logos', 'public');
            $validated['school_logo'] = '/storage/' . $path;
        }

        $achiever = Achiever::create($validated);
        Cache::forget('achievers_list');
        Cache::put('achievers_list', Achiever::orderBy('week_ending', 'desc')->get(), now()->addDays(7));

        return response()->json($achiever, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Achiever $achiever)
    {
        return response()->json($achiever);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Achiever $achiever)
    {
        $validated = $request->validate([
            'week_ending' => 'sometimes|date',
            'student_name' => 'sometimes|string',
            'student_school' => 'sometimes|string',
            'student_grade' => 'sometimes|string',
            'school_name' => 'sometimes|string',
            'school_location' => 'sometimes|string',
            'school_logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('school_logo')) {
            if ($achiever->school_logo && Storage::exists(str_replace('/storage/', 'public/', $achiever->school_logo))) {
                Storage::delete(str_replace('/storage/', 'public/', $achiever->school_logo));
            }

            $path = $request->file('school_logo')->store('logos', 'public');
            $validated['school_logo'] = '/storage/' . $path;
        }
        $achiever->update($validated);
        Cache::forget('achievers_list');
        Cache::put('achievers_list', Achiever::orderBy('week_ending', 'desc')->get(), now()->addDays(7));

        return response()->json($achiever);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Achiever $achiever)
    {
        // delete image
        if ($achiever->school_logo && Storage::exists(str_replace('/storage/', 'public/', $achiever->school_logo))) {
            Storage::delete(str_replace('/storage/', 'public/', $achiever->school_logo));
        }

        $achiever->delete();
        Cache::forget('achievers_list');
        Cache::put('achievers_list', Achiever::orderBy('week_ending', 'desc')->get(), now()->addDays(7));

        return response()->json(null, 204);
    }
}
