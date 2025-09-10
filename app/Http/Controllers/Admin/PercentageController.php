<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Percentage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PercentageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Percentage::query()->orderBy('created_at', 'desc');
        
        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('number', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $percentages = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $percentages,
            'message' => 'Percentages retrieved successfully'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Ready to create a new percentage'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric',
            'status' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $percentage = Percentage::create($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $percentage,
            'message' => 'Percentage created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $percentage = Percentage::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $percentage,
            'message' => 'Percentage retrieved successfully'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $percentage = Percentage::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $percentage,
            'message' => 'Ready to edit percentage'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $percentage = Percentage::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'number' => 'sometimes|required|numeric',
            'status' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $percentage->update($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $percentage,
            'message' => 'Percentage updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $percentage = Percentage::findOrFail($id);
        $percentage->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Percentage deleted successfully'
        ]);
    }
}
