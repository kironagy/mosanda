<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faqs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    protected $is_admin = false;
    public function __construct()
    {
        if(auth('sanctum')->user()){
            $this->is_admin = true;
        }else{
            $this->is_admin = false;
        }

    }
    public function index()
    {
        $appLang = app()->getLocale();
        $faqs = $this->is_admin ? Faqs::query()->get() : Faqs::where('status', 1)->get();
        
        $mappedFaqs = $faqs->map(function ($faq) use ($appLang) {
            return [
                'id' => $faq->id,
                'question' => $faq->getTranslation('question', $appLang),
                'answer' => $faq->getTranslation('answer', $appLang),
                'status' => $faq->status,
                'created_at' => $faq->created_at,
            ];
        });

        return response()->json(['data' => $mappedFaqs]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question.en' => 'required|string',
            'question.ar' => 'required|string',
            'answer.en' => 'required|string',
            'answer.ar' => 'required|string',
            'status' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $faq = Faqs::create([
            'question' => [
                'en' => $request->input('question.en'),
                'ar' => $request->input('question.ar'),
            ],
            'answer' => [
                'en' => $request->input('answer.en'),
                'ar' => $request->input('answer.ar'),
            ],
            'status' => $request->input('status', 1),
        ]);
        
        return response()->json(['data' => $faq, 'message' => 'FAQ created successfully'], 201);
    }
    
    public function show($id)
    {
        $faq = Faqs::findOrFail($id);
        return response()->json(['data' => $faq]);
    }
    
    public function update(Request $request, $id)
    {
        $faq = Faqs::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'question.en' => 'sometimes|required|string',
            'question.ar' => 'sometimes|required|string',
            'answer.en' => 'sometimes|required|string',
            'answer.ar' => 'sometimes|required|string',
            'status' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        if ($request->has('question')) {
            $faq->setTranslation('question', 'en', $request->input('question.en'));
            $faq->setTranslation('question', 'ar', $request->input('question.ar'));
        }
        
        if ($request->has('answer')) {
            $faq->setTranslation('answer', 'en', $request->input('answer.en'));
            $faq->setTranslation('answer', 'ar', $request->input('answer.ar'));
        }
        
        if ($request->has('status')) {
            $faq->status = $request->input('status');
        }
        
        $faq->save();
        
        return response()->json(['data' => $faq, 'message' => 'FAQ updated successfully']);
    }
    
    public function destroy($id)
    {
        $faq = Faqs::findOrFail($id);
        $faq->delete();
        
        return response()->json(['message' => 'FAQ deleted successfully']);
    }
}