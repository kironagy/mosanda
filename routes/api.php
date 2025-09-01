<?php

use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PakegeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\SetLangMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login" , [AuthController::class , 'login']);
Route::post("logout" , [AuthController::class , 'logout']);






Route::post('/pay/{id}', [PaymentController::class, 'pay']);
Route::post('/coingate/callback', [PaymentController::class, 'callback'])->name('coingate.callback');
Route::get('/coingate/success', [PaymentController::class, 'success'])->name('coingate.success');
Route::get('/coingate/cancel', [PaymentController::class, 'cancel'])->name('coingate.cancel');








// Handle

Route::post("calc-percentage" , [ContactController::class , 'calcPercentage']);

Route::middleware(SetLangMiddleware::class)->group(function(){
    // FAQ Routes
    Route::get('faqs', [FaqController::class, 'index']);
    // Service Routes
    Route::get('services', [ServiceController::class, 'index']);

    // Contact Routes
    Route::post('contacts', [ContactController::class, 'store']);

    // Package Routes
    Route::apiResource('pakeges', PakegeController::class)->only(['index' , 'show']);

    Route::middleware('auth:sanctum')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
    });
});

// Countries routes - read only
Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}', [CountryController::class, 'show']);

// Banks routes - read only
Route::get('banks', [BankController::class, 'index']);
Route::get('banks/{bank}', [BankController::class, 'show']);

