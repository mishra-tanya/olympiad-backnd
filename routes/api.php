<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\QuestionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/welcome', function () {
    return response()->json(['message' => 'Welcome to the Laravel API!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name("login"); 
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF token set']);
});

// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/class/{className}',[GoalController::class,'getGoals']);
Route::get('/class/{className}/goal/{goal}',[GoalController::class,'getTests']);

Route::get('/class/{className}/goal/{goalName}/{testName}',[QuestionController::class,'getQuestions']);

use App\Http\Controllers\ResultController;

Route::post('/results', [ResultController::class, 'store']); 
Route::get('/results/{userId}/{classId}/{goalId}/{testId}', [ResultController::class, 'getResultsByUser']); 

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/certificate/{certificate_id}', [ResultController::class, 'show'])->name('certificate.show');
Route::post('/generate-certificate', [ResultController::class, 'generateCertificate'])->name('certificate.generate');

use App\Http\Controllers\ProfileController;

Route::middleware('auth:sanctum')->put('/user/profile', [ProfileController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->get('/getuser/profile', [ProfileController::class, 'getProfile']);
use App\Http\Controllers\ContactController;

Route::post('/contact', [ContactController::class, 'contactMessages']);