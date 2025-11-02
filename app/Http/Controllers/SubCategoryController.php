<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryStoreRequest;
use App\Http\Requests\SubCategoryUpdateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class SubCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $subCategories = SubCategory::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'subCategories' => $subCategories,
            'categories' => $categories,
        ]);
    }

    public function store(SubCategoryStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $subCategory = SubCategory::create($validated);
        $subCategory->load('category');

        return response($subCategory, 201);
    }

    public function show(SubCategory $subCategory): Response
    {
        $subCategory->load('category');
        return response($subCategory);
    }

    public function update(SubCategoryUpdateRequest $request, SubCategory $subCategory): Response
    {
        $validated = $request->validated();

        $subCategory->update($validated);
        $subCategory->load('category');

        return response($subCategory);
    }

    public function destroy(SubCategory $subCategory): Response
    {
        $subCategory->delete();

        return response(null, 204);
    }

    public function getByCategory(Request $request, Category $category): Response
    {
        $subCategories = SubCategory::where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($subCategories);
    }

    public function showPage()
    {
        $subCategories = SubCategory::with('category.constructionType')->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('sub-categories/SubCategories', [
            'subCategories' => $subCategories,
            'categories' => $categories,
        ]);
    }
}

