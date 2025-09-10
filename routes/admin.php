<?php

use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PercentageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PakegeController;
use App\Http\Controllers\DashboarController;
use App\Http\Controllers\SettingController;

// FAQ Routes
Route::apiResource('faqs', FaqController::class);

// Service Routes
Route::apiResource('services', ServiceController::class);

// Contact Routes
Route::apiResource('contacts', ContactController::class);

// Package Routes
Route::apiResource('pakeges', PakegeController::class);

Route::apiResource('settings', SettingController::class)->only(['index', 'update']);
Route::get('dashboard', [DashboarController::class , 'getStatistics']);

// Countries routes
Route::apiResource('countries', CountryController::class);

// Orders
Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus']);

// Banks routes
Route::apiResource('banks', BankController::class);

// Percentages routes
Route::apiResource('percentages', PercentageController::class);
