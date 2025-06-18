<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index()
    {
        $vendors = Vendor::withCount('assets')->withSum('assets', 'purchase_price')->get();
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Add a new vendor via AJAX (similar to maintenance tasks).
     */
    public function addNewVendor(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendors'
        ]);

        try {
            $vendor = Vendor::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor added successfully',
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all vendors for dropdown.
     */
    public function getAllVendors(Request $request): JsonResponse
    {
        $query = Vendor::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $vendors = $query->orderBy('name')->get(['id', 'name']);
        return response()->json($vendors);
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if vendor has assets
        if ($vendor->assets()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Cannot delete vendor with existing assets. Please reassign or remove the assets first.');
        }

        $vendorName = $vendor->name;
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', "Vendor '{$vendorName}' has been deleted successfully.");
    }
}
