<?php

namespace App\Http\Controllers\Admin;

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
        $countries = Country::all();
        
        // Transform the countries collection to include localized names
        $countries = $countries->map(function($country) {
            $country->name = $country->name[app()->getLocale()];
            $country->code = $country->code;
            return $country;
        });

        return response()->json([
            'status' => true,
            'message' => 'Countries retrieved successfully',
            'data' => $countries
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
        ]);

        $country = Country::create([
            'code' => $request->code,
            'name' => [
                'en' => $request->name['en'],
                'ar' => $request->name['ar'],
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Country created successfully',
            'data' => $country
        ], 201);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
        ]);

        $country->update([
            'code' => $request->code,
            'name' => [
                'en' => $request->name['en'],
                'ar' => $request->name['ar'],
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Country updated successfully',
            'data' => $country
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json([
            'status' => true,
            'message' => 'Country deleted successfully'
        ]);
    }
}
