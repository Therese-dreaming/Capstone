<?php

namespace App\Http\Controllers;

use App\Models\NonRegisteredAsset;
use App\Models\RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonRegisteredAssetController extends Controller
{
    public function index()
    {
        $assets = NonRegisteredAsset::orderBy('created_at', 'desc')->paginate(10);
        return view('non-registered-assets.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'findings' => 'required|string',
            'remarks' => 'required|string',
            'ticket_number' => 'required|string|exists:repair_requests,ticket_number'
        ]);

        $repairRequest = RepairRequest::where('ticket_number', $validated['ticket_number'])->first();
        
        if (!$repairRequest) {
            return back()->with('error', 'Repair request not found.');
        }

        $asset = NonRegisteredAsset::create([
            ...$validated,
            'pulled_out_by' => Auth::user()->name,
            'pulled_out_at' => now(),
            'status' => 'PULLED OUT'
        ]);

        return redirect()->route('repair.details', ['id' => $repairRequest->id])
            ->with('success', 'Non-registered asset has been recorded successfully.');
    }

    public function update(Request $request, $id)
    {
        $asset = NonRegisteredAsset::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:DISPOSED,RETURNED',
            'disposal_details' => 'required_if:status,DISPOSED|nullable|string',
            'return_remarks' => 'required_if:status,RETURNED|nullable|string'
        ]);

        if ($validated['status'] === 'DISPOSED') {
            $asset->update([
                'status' => 'DISPOSED',
                'disposal_details' => $validated['disposal_details'],
                'disposed_at' => now(),
                'disposed_by' => Auth::user()->name
            ]);
        } else {
            $asset->update([
                'status' => 'RETURNED',
                'return_remarks' => $validated['return_remarks'],
                'returned_at' => now(),
                'returned_by' => Auth::user()->name
            ]);
        }

        return redirect()->route('repair.details', ['id' => $asset->repairRequest->id])
            ->with('success', 'Asset status has been updated successfully.');
    }
} 