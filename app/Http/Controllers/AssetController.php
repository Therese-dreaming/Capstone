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
                'category_id' => 'required|exists:categories,id', // Changed from 'category' to 'category_id'
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
                return back()
                    ->withInput($request->except('serial_number'))
                    ->with('showErrorModal', true)
                    ->with('errorMessage', 'This Serial Number is already registered in the system.');
            }

            $validated['serial_number'] = $request->serial_number;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }
    
            $asset = Asset::create($validated);
    
            // QR code generation with text
            // Update QR code generation to use category relationship
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'name' => $asset->name,
                'category_id' => $asset->category_id, // Updated to use category_id
                'serial_number' => $asset->serial_number,
                'location' => $asset->location,
                'status' => $asset->status
            ]));

            // Create QR code with writer
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Create a new image with space for text
            $qrImage = imagecreatefromstring($result->getString());
            $newHeight = imagesx($qrImage) + 100; // Add 100px for text
            $newImage = imagecreatetruecolor(imagesx($qrImage), $newHeight);
            $white = imagecolorallocate($newImage, 255, 255, 255);
            $black = imagecolorallocate($newImage, 0, 0, 0);
            
            // Fill background
            imagefill($newImage, 0, 0, $white);
            
            // Copy QR code to new image
            imagecopy($newImage, $qrImage, 0, 0, 0, 0, imagesx($qrImage), imagesy($qrImage));
            
            // Add text
            $font = 5; // Built-in font
            $assetName = $asset->name;
            $serialNumber = $asset->serial_number;
            
            // Center and add text
            $nameX = (imagesx($qrImage) - strlen($assetName) * imagefontwidth($font)) / 2;
            $serialX = (imagesx($qrImage) - strlen($serialNumber) * imagefontwidth($font)) / 2;
            
            imagestring($newImage, $font, $nameX, imagesy($qrImage) + 20, $assetName, $black);
            imagestring($newImage, $font, $serialX, imagesy($qrImage) + 40, $serialNumber, $black);
            
            // Save the image
            ob_start();
            imagepng($newImage);
            $imageData = ob_get_clean();
            
            // Clean up
            imagedestroy($qrImage);
            imagedestroy($newImage);
            
            $qrPath = 'qrcodes/asset-' . $asset->id . '.png';
            Storage::disk('public')->put($qrPath, $imageData);
    
            $asset->update(['qr_code' => $qrPath]);
    
            return redirect()->route('assets.list')->with('success', 'Asset created successfully');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput($request->except('serial_number'));
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
