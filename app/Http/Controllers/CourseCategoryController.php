<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    /**
     * Admin: List categories
     */
    public function adminIndex()
    {
        $this->authorize('isAdmin');

        $categories = CourseCategory::orderBy('sort_order')
            ->paginate(15);

        return view('admin.course-categories.index', compact('categories'));
    }

    /**
     * Admin: Create category form
     */
    public function adminCreate()
    {
        $this->authorize('isAdmin');

        return view('admin.course-categories.create');
    }

    /**
     * Admin: Store new category
     */
    public function adminStore(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name' => 'required|string|unique:course_categories',
            'slug' => 'nullable|string|unique:course_categories',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[a-f0-9]{6}$/i',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        CourseCategory::create($validated);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Admin: Edit category form
     */
    public function adminEdit(CourseCategory $category)
    {
        $this->authorize('isAdmin');

        return view('admin.course-categories.edit', compact('category'));
    }

    /**
     * Admin: Update category
     */
    public function adminUpdate(Request $request, CourseCategory $category)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name' => 'required|string|unique:course_categories,name,' . $category->id,
            'slug' => 'nullable|string|unique:course_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[a-f0-9]{6}$/i',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Admin: Delete category
     */
    public function adminDestroy(CourseCategory $category)
    {
        $this->authorize('isAdmin');

        $category->delete();

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
