<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::first();
        return response()->json($settings);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $settings)
    {
        $setting = Setting::where('id', 1)->first();
        $setting->update($request->all());
        return response()->json($setting);
    }

}
