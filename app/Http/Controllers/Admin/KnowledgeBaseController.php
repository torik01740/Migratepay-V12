<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiKnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    /**
     * Admin: List all knowledge base items.
     */
    public function index(): JsonResponse
    {
        $kbs = AiKnowledgeBase::orderBy('created_at', 'desc')->get();
        return response()->json($kbs);
    }

    /**
     * Admin: Store a new Q&A item.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:255',
            'status'   => 'required|in:Active,Draft,Inactive',
        ]);

        $kb = AiKnowledgeBase::create($validated);
        return response()->json(['success' => true, 'data' => $kb], 201);
    }

    /**
     * Admin: Update Q&A item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $kb = AiKnowledgeBase::findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:255',
            'status'   => 'required|in:Active,Draft,Inactive',
        ]);

        $kb->update($validated);
        return response()->json(['success' => true, 'data' => $kb]);
    }

    /**
     * Admin: Delete Q&A item.
     */
    public function destroy(int $id): JsonResponse
    {
        $kb = AiKnowledgeBase::findOrFail($id);
        $kb->delete();
        return response()->json(['success' => true]);
    }
}
