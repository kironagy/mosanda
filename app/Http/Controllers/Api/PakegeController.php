<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pakeges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PakegeController extends Controller
{
    public function index()
    {
        $appLang = app()->getLocale();
        
        $pakeges = Pakeges::all()->map(function ($pakege) use ($appLang) {
            return [
                'id' => $pakege->id,
                'title' => $pakege->getTranslation('title', $appLang),
                'description' => $pakege->getTranslation('description', $appLang),
                'image' => asset('storage/' . $pakege->image),
                'price' => $pakege->price,
                'amount_from' => $pakege->amount_from,
                'amount_to' => $pakege->amount_to,
                'support_percentage' => $pakege->support_percentage,
                'created_at' => $pakege->created_at,
            ];
        });

        return response()->json(['data' => $pakeges]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'amount_from' => 'required|numeric|min:0',
            'amount_to' => 'required|numeric|min:0',
            'support_percentage' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Handle image upload
        $imagePath = $request->file('image')->store('pakeges', 'public');
        
        $pakege = Pakeges::create([
            'title' => [
                'en' => $request->input('title.en'),
                'ar' => $request->input('title.ar'),
            ],
            'description' => [
                'en' => $request->input('description.en', ''),
                'ar' => $request->input('description.ar', ''),
            ],
            'image' => $imagePath,
            'amount_from' => $request->input('amount_from'),
            'amount_to' => $request->input('amount_to'),
            'support_percentage' => $request->input('support_percentage'),
            'price' => $request->input('price'),
        ]);
        
        return response()->json(['data' => $pakege, 'message' => 'Package created successfully'], 201);
    }
    
    public function show(Pakeges $pakege)
    {
        return response()->json(['data' => $pakege]);
    }
    
    public function update(Request $request, $id)
    {
        $pakege = Pakeges::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title.en' => 'sometimes|required|string',
            'title.ar' => 'sometimes|required|string',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount_from' => 'sometimes|required|numeric|min:0',
            'amount_to' => 'sometimes|required|numeric|min:0',
            'support_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'price' => 'sometimes|required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        if ($request->has('title')) {
            $pakege->setTranslation('title', 'en', $request->input('title.en'));
            $pakege->setTranslation('title', 'ar', $request->input('title.ar'));
        }
        
        if ($request->has('description')) {
            $pakege->setTranslation('description', 'en', $request->input('description.en', ''));
            $pakege->setTranslation('description', 'ar', $request->input('description.ar', ''));
        }
        
        if ($request->hasFile('image')) {
            // Delete old image
            if (Storage::disk('public')->exists($pakege->image)) {
                Storage::disk('public')->delete($pakege->image);
            }
            
            // Upload new image
            $pakege->image = $request->file('image')->store('pakeges', 'public');
        }
        
        if ($request->has('amount_from')) {
            $pakege->amount_from = $request->input('amount_from');
        }
        
        if ($request->has('amount_to')) {
            $pakege->amount_to = $request->input('amount_to');
        }
        
        if ($request->has('support_percentage')) {
            $pakege->support_percentage = $request->input('support_percentage');
        }
        
        if ($request->has('price')) {
            $pakege->price = $request->input('price');
        }
        
        $pakege->save();
        
        return response()->json(['data' => $pakege, 'message' => 'Package updated successfully']);
    }
    
    public function destroy($id)
    {
        $pakege = Pakeges::findOrFail($id);
        
        // Delete image
        if (Storage::disk('public')->exists($pakege->image)) {
            Storage::disk('public')->delete($pakege->image);
        }
        
        $pakege->delete();
        
        return response()->json(['message' => 'Package deleted successfully']);
    }
}