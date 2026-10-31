<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiContentService;
use Illuminate\Http\Request;

class AiAssistanceController extends Controller
{
    protected $aiService;

    public function __construct(AiContentService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function correctText(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'context_type' => 'nullable|string' // 'producto', 'servicio', 'post'
        ]);

        $result = $this->aiService->magicalFix(
            $request->input('text'),
            $request->input('context_type', 'general')
        );

        return response()->json($result);
    }
}
