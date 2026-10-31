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
            'text' => 'required|string|max:10000',
            'context_type' => 'nullable|string'
        ]);

        $result = $this->aiService->magicalFix(
            $request->input('text'),
            $request->input('context_type', 'oferta_empleo')
        );

        return response()->json($result);
    }
}
