<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::orderBy('name')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $excludeIds = $this->excludedParentIds($category);
        $parentCategories = Category::query()
            ->whereNotIn('id', $excludeIds)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $parentId = isset($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        if ($parentId === $category->id) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        if ($parentId !== null && $this->isDescendantOf($parentId, $category)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['parent_id' => 'Cannot set parent to a descendant of this category.']);
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete category while products are assigned to it.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * @return array<int, int>
     */
    private function excludedParentIds(Category $category): array
    {
        return array_values(array_unique(array_merge(
            [$category->id],
            $this->descendantIds($category)
        )));
    }

    /**
     * @return array<int, int>
     */
    private function descendantIds(Category $category): array
    {
        $ids = [];
        $queue = Category::where('parent_id', $category->id)->pluck('id')->all();

        while ($queue !== []) {
            $id = (int) array_shift($queue);
            $ids[] = $id;
            $children = Category::where('parent_id', $id)->pluck('id')->all();
            foreach ($children as $childId) {
                $queue[] = (int) $childId;
            }
        }

        return $ids;
    }

    private function isDescendantOf(int $candidateParentId, Category $category): bool
    {
        return in_array($candidateParentId, $this->descendantIds($category), true);
    }
}
