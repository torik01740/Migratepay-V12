<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * List all categories (alphabetical).
     */
    public function index(): JsonResponse
    {
        $categories = Category::alphabetical()->get();
        return response()->json([
            'success'    => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Admin: Store a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'slug'        => 'nullable|string|max:100|unique:categories,slug',
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::create([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active'   => true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }

    /**
     * Admin: Update category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $id,
            'slug'        => 'nullable|string|max:100|unique:categories,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $category->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? $category->description,
            'is_active'   => $validated['is_active'] ?? $category->is_active,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Category updated.',
            'category' => $category,
        ]);
    }

    /**
     * Admin: Delete category.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted.',
        ]);
    }
}
