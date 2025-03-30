<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Category added successfully');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        try {
            // Check if category has assets
            if ($category->assets()->count() > 0) {
                return back()->with('error', 'Cannot delete category: It still has assets assigned to it.');
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Category "' . $categoryName . '" has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
