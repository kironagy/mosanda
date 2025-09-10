<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Percentage;
use Illuminate\Http\Request;

class PercentageController extends Controller
{
    /**
     * Get all percentage records with optional search functionality
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $percentages = Percentage::where('status', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $percentages,
            'message' => 'Percentages retrieved successfully'
        ]);
    }
}
