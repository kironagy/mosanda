<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $appLang = app()->getLocale();
        $services = Services::all()->map(function ($service) use ($appLang) {
            return [
                'id' => $service->id,
                'title' => $service->getTranslation('title', $appLang),
                'description' => $service->getTranslation('description', $appLang),
                'image' => asset('storage/' . $service->image),
                'created_at' => $service->created_at,
            ];
        });
        return response()->json(['data' => $services]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'button_text' => 'nullable|string',
            'button_link' => 'nullable|url',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Handle image upload
        $imagePath = $request->file('image')->store('services', 'public');
        
        $service = Services::create([
            'title' => [
                'en' => $request->input('title.en'),
                'ar' => $request->input('title.ar'),
            ],
            'description' => [
                'en' => $request->input('description.en'),
                'ar' => $request->input('description.ar'),
            ],
            'image' => $imagePath,
            'button_text' => $request->input('button_text'),
            'button_link' => $request->input('button_link'),
        ]);
        
        return response()->json(['data' => $service, 'message' => 'Service created successfully'], 201);
    }
    
    public function show($id)
    {
        $service = Services::findOrFail($id);
        return response()->json(['data' => $service]);
    }
    
    public function update(Request $request, $id)
    {
        $service = Services::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title.en' => 'sometimes|required|string',
            'title.ar' => 'sometimes|required|string',
            'description.en' => 'sometimes|required|string',
            'description.ar' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string',
            'button_link' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        if ($request->has('title')) {
            $service->setTranslation('title', 'en', $request->input('title.en'));
            $service->setTranslation('title', 'ar', $request->input('title.ar'));
        }
        
        if ($request->has('description')) {
            $service->setTranslation('description', 'en', $request->input('description.en'));
            $service->setTranslation('description', 'ar', $request->input('description.ar'));
        }
        
        if ($request->hasFile('image')) {
            // Delete old image
            if (Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            
            // Upload new image
            $service->image = $request->file('image')->store('services', 'public');
        }
        
        $service->button_text = $request->input('button_text', $service->button_text);
        $service->button_link = $request->input('button_link', $service->button_link);
        
        $service->save();
        
        return response()->json(['data' => $service, 'message' => 'Service updated successfully']);
    }
    
    public function destroy($id)
    {
        $service = Services::findOrFail($id);
        
        // Delete image
        if (Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }
        
        $service->delete();
        
        return response()->json(['message' => 'Service deleted successfully']);
    }
}