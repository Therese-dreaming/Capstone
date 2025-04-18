<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller 
{
    public function index()
    {
        $assets = Asset::all();
        return view('asset-list', compact('assets'));
    }

    public function store(Request $request)
    {
        try {
            // First validate everything except serial number
            $validated = $request->validate([
                'name' => 'required',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required',
                'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PENDING DEPLOYMENT',
                'model' => 'required',
                'specification' => 'required',
                'vendor' => 'required',
                'purchase_date' => 'required|date',
                'warranty_period' => 'required|date',
                'lifespan' => 'required|integer',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'purchase_price' => 'required|numeric|min:0', // Add this line
            ]);

            // Check for duplicate serial number
            if (Asset::where('serial_number', $request->serial_number)->exists()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'error' => 'This Serial Number is already registered in the system.'
                    ], 422);
                }
                return back()->withErrors(['serial_number' => 'This Serial Number is already registered in the system.']);
            }

            $validated['serial_number'] = $request->serial_number;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }
    
            $asset = Asset::create($validated);

            // QR code generation
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'name' => $asset->name,
                'category_id' => $asset->category_id,
                'serial_number' => $asset->serial_number
            ]));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $qrPath = 'qrcodes/asset-' . $asset->id . '.png';
            Storage::disk('public')->put($qrPath, $result->getString());
            
            $asset->update(['qr_code' => $qrPath]);
    
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Asset added successfully']);
            }
            
            return redirect()->route('assets.index')->with('success', 'Asset has been added successfully');
        
        } catch (\Exception $e) {
            \Log::error('Asset creation error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'An error occurred while creating the asset. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while creating the asset. Please try again.');
        }
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('add-asset', compact('categories'));
    }

    public function qrList()
    {
        $assets = Asset::all();
        return view('qr-list', compact('assets'));
    }

    public function previewQrCodes(Request $request)
    {
        $selectedIds = json_decode($request->selected_items);
        $assets = Asset::whereIn('id', $selectedIds)->get();
        
        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->stream('preview.pdf');
    }

    public function exportQrCodes(Request $request)
    {
        $selectedIds = json_decode($request->selected_items);
        $assets = Asset::whereIn('id', $selectedIds)->get();
        
        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->download('asset-qrcodes.pdf');
    }

    public function categoryReport(Request $request)
    {
        $query = Asset::query();
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
    
        $categories = Category::all();
        
        // Prepare category statistics
        $categoryStats = Category::withCount('assets')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->assets_count,
                    'total_value' => $category->assets->sum('purchase_price')
                ];
            });
    
        // Prepare detailed category information
        $categoryDetails = Category::all()->map(function ($category) {
            $assets = $category->assets;
            return [
                'name' => $category->name,
                'total' => $assets->count(),
                'in_use' => $assets->where('status', 'IN USE')->count(),
                'under_repair' => $assets->where('status', 'UNDER REPAIR')->count(),
                'disposed' => $assets->where('status', 'DISPOSED')->count(),
                'value' => $assets->sum('purchase_price')
            ];
        });
    
        return view('asset-category-report', compact('categories', 'categoryStats', 'categoryDetails'));
    }

    public function destroy(Asset $asset)
    {
        // Create a history record for deletion
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'STATUS',
            'old_value' => $asset->status,
            'new_value' => 'DELETED',
            'remarks' => 'Asset was deleted from the system',
            'changed_by' => auth()->id()
        ]);
    
        $asset->delete();
    
        return redirect()->route('assets.index')
            ->with('success', 'Asset has been deleted successfully');
    }
    
    public function dispose(Asset $asset, Request $request)
    {
        $asset->status = 'DISPOSED';
        $asset->disposal_date = now();
        $asset->disposal_reason = $request->disposal_reason;
        $asset->save();
    
        // Create asset history record
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'STATUS',
            'old_value' => 'IN USE',
            'new_value' => 'DISPOSED',
            'remarks' => 'Asset marked as disposed. Reason: ' . $request->disposal_reason,
            'changed_by' => auth()->id()
        ]);
    
        if ($request->has('redirect')) {
            return redirect($request->redirect)->with('success', 'Asset has been marked as disposed.');
        }
    
        return redirect()->route('assets.index')->with('success', 'Asset has been marked as disposed.');
    }

    public function edit(Asset $asset)
    {
        $categories = Category::all();
        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required',
            'serial_number' => 'required',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            // In both store and update methods, update the status validation:
            'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PENDING DEPLOYMENT',
            'model' => 'required',
            'specification' => 'required',
            'vendor' => 'required',
            'purchase_date' => 'required|date',
            'warranty_period' => 'required|date',
            'lifespan' => 'required|integer',
        ]);
    
        // Handle location field
        if ($request->location_select === 'others') {
            $validated['location'] = $request->location;
        } else {
            $validated['location'] = $request->location_select;
        }
    
        // Check for duplicate serial number, excluding the current asset
        if (Asset::where('serial_number', $request->serial_number)
                ->where('id', '!=', $asset->id)
                ->exists()) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'This Serial Number is already registered in the system.']);
        }
    
        $oldValues = $asset->getAttributes();
    
        // Check if any QR-relevant fields are being updated
        $qrRelevantFields = ['name', 'serial_number', 'category_id'];
        $needsQRUpdate = false;
        
        foreach ($qrRelevantFields as $field) {
            if (isset($validated[$field]) && $validated[$field] != $oldValues[$field]) {
                $needsQRUpdate = true;
                break;
            }
        }
    
        // If relevant fields changed, regenerate QR code
        if ($needsQRUpdate) {
            // Delete old QR code if it exists
            if ($asset->qr_code && Storage::exists('public/' . $asset->qr_code)) {
                Storage::delete('public/' . $asset->qr_code);
            }
    
            // Generate new QR code using Endroid - match the store method structure
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'serial_number' => $validated['serial_number']
            ]));
    
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Use the same naming convention as store method
            $qrPath = 'qrcodes/asset-' . $asset->id . '.png';
            Storage::disk('public')->put($qrPath, $result->getString());
            
            // Update the asset with new QR code path
            $validated['qr_code'] = $qrPath;
        }
    
        // Update the asset first to ensure all new values are saved
        $asset->update($validated);
    
        // Record changes in asset history
        foreach ($validated as $field => $value) {
            if ($oldValues[$field] != $value) {
                // Change purchase_price to PRICE for consistency
                $changeType = $field === 'purchase_price' ? 'PRICE' : strtoupper($field);
                
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => $changeType,
                    'old_value' => $oldValues[$field],
                    'new_value' => $value,
                    'changed_by' => auth()->id()
                ]);
            }
        }
    
        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully');
    }
}
