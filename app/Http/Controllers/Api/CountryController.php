<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locale = app()->getLocale();
        $countries = Country::all()->map(function($country) use ($locale) {
            $country->name = $country->name[$locale];
            return $country;
        });

        return response()->json([
            'status' => true,
            'message' => 'Countries retrieved successfully',
            'data' => $countries
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        return response()->json([
            'status' => true,
            'message' => 'Country retrieved successfully',
            'data' => $country
        ]);
    }
}
