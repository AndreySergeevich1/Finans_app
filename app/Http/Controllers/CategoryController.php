<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Auth::user()->categories()->get();
        return response()->json($categories);
    }

    public function create()
    {
        // Получаем список категорий для выбора родительской
        $potentialParents = Auth::user()
                                ->categories()
                                ->orderBy('name')
                                ->get(['id', 'name']);

        return Inertia::render('Categories/Create', [
            'potentialParents' => $potentialParents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['required', 'string', 'max:50'],
        ]);

        $validated['user_id'] = Auth::id();

        Category::create($validated);

        return redirect()->back()->with('success', 'Категория успешно добавлена.');
    }

    public function show(Category $category)
    {
         if ($category->user_id !== Auth::id()) abort(403);
         return redirect()->route('categories.edit', $category);
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        $potentialParents = Auth::user()
            ->categories()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'potentialParents' => $potentialParents,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['required', 'string', 'max:50'],
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Категория успешно обновлена.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Категория успешно удалена.');
    }
}
