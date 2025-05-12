<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CertificateVerifyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\RazorpayController;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


//public route
Route::get('/welcome', function () {
    return response()->json(['message' => 'Welcome to the Laravel API!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name("login"); 
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF token set']);
});
Route::post('/contact', [ContactController::class, 'contactMessages']);
Route::get('/certificate/{certificate_id}', [ResultController::class, 'show'])->name('certificate.show');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/goal/{goal}',[GoalController::class,'goalName']);

//user authentication required i.e. user routes
Route::middleware('auth:sanctum')->group(function () {

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/class/{className}',[GoalController::class,'getGoals']);
Route::get('/class/{className}/goal/{goal}',[GoalController::class,'getTests']);

Route::get('/class/{className}/goal/{goalName}/{testName}',[QuestionController::class,'getQuestions']);

Route::post('/results', [ResultController::class, 'store']); 
Route::get('/results/{userId}/{classId}/{goalId}/{testId}', [ResultController::class, 'getResultsByUser']); 
Route::post('/generate-certificate', [ResultController::class, 'generateCertificate'])->name('certificate.generate');

Route::post('/logout', [AuthController::class, 'logout']);

Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
Route::get('/getuser/profile', [ProfileController::class, 'getProfile']);

Route::get('/getOverall', [DashboardController::class, 'getOverall']);
Route::get('/getByClass', [DashboardController::class, 'getByClass']);

Route::post('/certificateVerification/{certificateId}',[CertificateVerifyController::class,'verifyCertificate']);
Route::get('/getAllCertificates',[CertificateVerifyController::class,'getAllCertificates']);

Route::get('/check-class-completion', [CertificateController::class, 'checkClassCompletion']);
Route::get('/check-goal-completion', [CertificateController::class, 'checkGoalCompletion']);
Route::get('/check-class-payment', [CertificateController::class, 'checkClassPayment']);

Route::post('/create-order', [RazorpayController::class, 'createOrder']);
Route::post('/payment-success', [RazorpayController::class, 'handlePaymentSuccess']);
Route::get('/check-class-payment', [CertificateController::class, 'checkClassPayment']);

});

//admin role ; admin authentication required
Route::middleware('admin','auth:sanctum')->group(function () {

Route::get('/getUsers',[AdminController::class,'getUsersWithUserRole']);
Route::get('/getAllCertificate',[AdminController::class,'getAllCertificate']);
Route::get('/getCertificateUser/{userId}',[AdminController::class,'getCertificateUser']);
Route::get('/getResults/{userId}',[AdminController::class,'getResults']);
Route::get('/getGoals',[AdminController::class,'getGoals']);
Route::get('/getTests',[AdminController::class,'getTests']);
Route::get('/getTestQuestions',[AdminController::class,'getTestQuestions']);
Route::get('/contact',[AdminController::class,'contact']);
Route::get('/dashboard',[AdminController::class,'dashboard']);
Route::get('/track', [AdminController::class, 'getUserRegistrations']);

Route::get('/geographic-distribution', [AdminDashController::class, 'getUserCountryDistribution']);
Route::get('/by-class', [AdminDashController::class, 'getUserClass']);
Route::get('/user-segmentation', [AdminDashController::class, 'behaviorDistribution']);
Route::get('/topPerformers', [AdminDashController::class, 'topPerformers']);
Route::get('/getTestsGiven', [AdminDashController::class, 'getTestsGiven']);
Route::get('/getTestsGiven', [AdminDashController::class, 'getTestsGiven']);
Route::get('/getUserCompletedGoals', [AdminDashController::class, 'getUserCompletedGoals']);

Route::get('/users/emails', [UserController::class, 'getEmails']);
Route::post('/send-email', [EmailController::class, 'send']);

});