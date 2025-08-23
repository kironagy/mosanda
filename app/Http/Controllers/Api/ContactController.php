<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contacts::query();

        // Apply search if search parameter exists
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('bank_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('support_for', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Get pagination parameters from request
        $limit = $request->input('limit', 10); // Default limit is 10
        $page = $request->input('page', 1);    // Default page is 1

        // Get paginated results
        $contacts = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => $contacts->items(),
            'pagination' => [
                'total' => $contacts->total(),
                'per_page' => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'from' => $contacts->firstItem(),
                'to' => $contacts->lastItem()
            ]
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
            'amount' => 'required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $contact = Contacts::create($request->all());
        
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