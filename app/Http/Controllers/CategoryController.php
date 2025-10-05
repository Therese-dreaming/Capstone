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
            $categoryName = $category->name;
            
            // Check if category is referenced in other tables
            $references = $this->checkCategoryReferences($category);
            
            if (!empty($references)) {
                $referencesList = implode(', ', $references);
                return back()->with('error', 
                    'Cannot delete category "' . $categoryName . '" because it is referenced in: ' . $referencesList . '. 
                    Please reassign or remove these records first.');
            }

            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Category "' . $categoryName . '" has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }

    /**
     * Check if category is referenced in other tables
     */
    private function checkCategoryReferences(Category $category)
    {
        $references = [];

        // Check assets
        if ($category->assets()->exists()) {
            $assetCount = $category->assets()->count();
            $references[] = "Assets ($assetCount items)";
        }

        // Check repair requests
        if ($category->repairRequests()->exists()) {
            $repairCount = $category->repairRequests()->count();
            $references[] = "Repair Requests ($repairCount items)";
        }

        return $references;
    }
}
