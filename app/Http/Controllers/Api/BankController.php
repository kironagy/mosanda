<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bank::with('country');
        
        // Filter by country_id if provided
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        
        $banks = $query->get();
        
        // Transform the data to include localized names
        $locale = app()->getLocale();
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
}
