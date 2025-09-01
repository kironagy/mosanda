<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locale = app()->getLocale();
        $banks = Bank::with('country')->get();
        
        $transformedBanks = $banks->map(function($bank) use ($locale) {
            return [
                'id' => $bank->id,
                'name' => $bank->name[$locale],
                'country_id' => $bank->country_id,
                'created_at' => $bank->created_at,
                'updated_at' => $bank->updated_at,
                'country' => [
                    'id' => $bank->country->id,
                    'name' => $bank->country->name[$locale],
                    'created_at' => $bank->country->created_at,
                    'updated_at' => $bank->country->updated_at,
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Banks retrieved successfully',
            'data' => $transformedBanks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $bank = Bank::create([
            'name' => [
                'en' => $request->name['en'],
                'ar' => $request->name['ar'],
            ],
            'country_id' => $request->country_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bank created successfully',
            'data' => $bank
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        $bank->load('country');
        return response()->json([
            'status' => true,
            'message' => 'Bank retrieved successfully',
            'data' => $bank
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $bank->update([
            'name' => [
                'en' => $request->name['en'],
                'ar' => $request->name['ar'],
            ],
            'country_id' => $request->country_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bank updated successfully',
            'data' => $bank
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return response()->json([
            'status' => true,
            'message' => 'Bank deleted successfully'
        ]);
    }
}
