<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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
                'status' => 'required|in:IN USE,UNDER REPAIR,DISPOSED,UPGRADE,PENDING DEPLOYMENT',
                'model' => 'required',
                'specification' => 'required',
                'vendor' => 'required',
                'purchase_date' => 'required|date',
                'warranty_period' => 'required|date',
                'lifespan' => 'required|integer',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Check for duplicate serial number
            if (Asset::where('serial_number', $request->serial_number)->exists()) {
                return response()->json([
                    'error' => 'This Serial Number is already registered in the system.'
                ], 422);
            }

            $validated['serial_number'] = $request->serial_number;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }
    
            $asset = Asset::create($validated);

            // Simplified QR code generation
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
    
            return redirect()->route('assets.list')->with('success', 'Asset added successfully');
        
        } catch (\Exception $e) {
            \Log::error('Asset creation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while creating the asset. Please try again.'
            ], 500);
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
}
