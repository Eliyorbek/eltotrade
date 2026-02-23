<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CategoryRequest;
use App\Models\Category;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:categories.view', only: ['index']),
            new Middleware('permission:categories.create', only: ['create', 'store']),
            new Middleware('permission:categories.edit', only: ['edit', 'update']),
            new Middleware('permission:categories.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(10);

        return view('backend.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('backend.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategoriya muvaffaqiyatli qo\'shildi!');
    }

    public function edit(Category $category)
    {
        return view('backend.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategoriya muvaffaqiyatli yangilandi!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Bu kategoriyada mahsulotlar mavjud, o\'chirib bo\'lmaydi!');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategoriya o\'chirildi!');
    }
}
