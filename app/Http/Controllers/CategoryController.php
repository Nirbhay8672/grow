<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\ConstructionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return response($categories);
    }

    public function store(CategoryStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $category = Category::create($validated);

        return response($category, 201);
    }

    public function show(Category $category): Response
    {
        return response($category);
    }

    public function update(CategoryUpdateRequest $request, Category $category): Response
    {
        $validated = $request->validated();

        $category->update($validated);

        return response($category);
    }

    public function destroy(Category $category): Response
    {
        $category->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $categories = Category::with('constructionType')
            ->orderBy('name')
            ->get();
        
        $constructionTypes = ConstructionType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return Inertia::render('categories/Categories', [
            'categories' => $categories,
            'constructionTypes' => $constructionTypes,
        ]);
    }
}

