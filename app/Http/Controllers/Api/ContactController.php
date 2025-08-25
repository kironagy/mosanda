<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // Get all contacts first
        $contacts = Contacts::all();

        // Apply search if search parameter exists
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $contacts = $contacts->filter(function($contact) use ($searchTerm) {
                return str_contains(strtolower($contact->name), strtolower($searchTerm)) ||
                       str_contains(strtolower($contact->phone), strtolower($searchTerm)) ||
                       str_contains(strtolower($contact->bank_name), strtolower($searchTerm)) ||
                       str_contains(strtolower($contact->support_for), strtolower($searchTerm)) ||
                       str_contains(strtolower($contact->status), strtolower($searchTerm));
            });
        }

        // Get pagination parameters from request
        $limit = $request->input('limit', 10); // Default limit is 10
        $page = $request->input('page', 1);    // Default page is 1

        // Manual pagination
        $total = $contacts->count();
        $contacts = $contacts->forPage($page, $limit);

        return response()->json([
            'data' => $contacts->values()->all(),
            'pagination' => [
                'total' => $total,
                'per_page' => $limit,
                'current_page' => $page,
                'last_page' => ceil($total / $limit),
                'from' => ($page - 1) * $limit + 1,
                'to' => min($page * $limit, $total)
            ]
        ]);
    }
    public function calcPercentage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'support_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $support_amount = $request->support_amount;
        $data = Setting::first();
        $supportPercentage = $data['subscription_fee_percentage'];
        // $supportAmount = $totalNeedAmount * ($supportPercentage / 100);
        $supportAmount = $support_amount * ($supportPercentage / 100);

        return response()->json([
            'amount' => $supportAmount,
        ]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'bank_name' => 'required|string',
            'bank_iban' => 'required|string',
            'support_for' => 'required|string',
            'total_need_amount' => 'required|numeric|min:0',
            'support_percentage' => 'required|numeric|min:0|max:100',
            'support_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = Setting::first();
        $supportPercentage = $data['subscription_fee_percentage'] ?? 0;
        $amount = $request->support_amount * ($supportPercentage / 100);

        $requestData = $request->all();
        $requestData['amount'] = $amount;
        
        $contact = Contacts::create($requestData);
        
        return response()->json(['data' => $contact, 'message' => 'Contact created successfully'], 201);
    }
    
    public function show($id)
    {
        $contact = Contacts::findOrFail($id);
        return response()->json(['data' => $contact]);
    }
    
    public function update(Request $request, $id)
    {
        $contact = Contacts::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'bank_name' => 'sometimes|required|string|max:255',
            'bank_iban' => 'sometimes|required|string|max:50',
            'support_for' => 'sometimes|required|string|max:255',
            'total_need_amount' => 'sometimes|required|numeric|min:0',
            'support_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'support_amount' => 'sometimes|required|numeric|min:0',
            'amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string',
            'message' => 'sometimes|required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $contact->update($request->all());
        
        return response()->json(['data' => $contact, 'message' => 'Contact updated successfully']);
    }
    
    public function destroy($id)
    {
        $contact = Contacts::findOrFail($id);
        $contact->delete();
        
        return response()->json(['message' => 'Contact deleted successfully']);
    }
}