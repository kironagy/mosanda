<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PakegeController;



// FAQ Routes
Route::apiResource('faqs', FaqController::class);

// Service Routes
Route::apiResource('services', ServiceController::class);

// Contact Routes
Route::apiResource('contacts', ContactController::class);

// Package Routes
Route::apiResource('pakeges', PakegeController::class);