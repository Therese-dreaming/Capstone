<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\BorrowableAsset;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BorrowingController extends Controller
{
    /**
     * Display the borrowing dashboard
     */
    public function dashboard()
    {
        // Get statistics for the dashboard
        $totalAssets = BorrowableAsset::whereIn('status', ['active', 'in_use'])->count();
        $availableAssets = BorrowableAsset::where('status', 'active')->count();
        $borrowedAssets = BorrowableAsset::where('status', 'in_use')->count();
        
        // Get recent borrowing activity
        $recentBorrowings = Borrowing::with(['borrower', 'items.borrowableAsset'])
            ->latest()
            ->take(10)
            ->get();
        
        // Get assets that can be borrowed (active status)
        $borrowableAssets = BorrowableAsset::with(['category', 'location'])
            ->where('status', 'active')
            ->orderBy('name')
            ->take(10)
            ->get();

        return view('borrowing.dashboard', compact(
            'totalAssets',
            'availableAssets',
            'borrowedAssets',
            'recentBorrowings',
            'borrowableAssets'
        ));
    }

    /**
     * Show the RFID borrowing form
     */
    public function create()
    {
        // Get all available assets for borrowing
        $availableAssets = BorrowableAsset::with(['category', 'location'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get count of available assets by category
        $assetsByCategory = BorrowableAsset::select('category_id', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->with('category')
            ->groupBy('category_id')
            ->get();

        return view('borrowing.create', compact('availableAssets', 'assetsByCategory'));
    }

    /**
     * Handle RFID scan and borrowing process
     */
    public function handleRfidBorrowing(Request $request)
    {
        try {
            $request->validate([
                'rfid_number' => 'required|string',
                'asset_ids' => 'required|array|min:1',
                'asset_ids.*' => 'exists:borrowable_assets,id',
                'purpose' => 'required|string|max:255',
                'expected_return_date' => 'required|date|after_or_equal:today',
                'notes' => 'nullable|string',
            ]);

            // Find user by RFID
            $user = User::where('rfid_number', $request->rfid_number)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'first_time' => true,
                    'message' => 'First time RFID scan. Registration required.'
                ]);
            }

            // Check if all selected assets are available
            $assets = BorrowableAsset::whereIn('id', $request->asset_ids)
                ->where('status', 'active')
                ->get();

            if ($assets->count() !== count($request->asset_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected assets are no longer available.'
                ]);
            }

            // Create borrowing record
            DB::beginTransaction();

            $borrowing = Borrowing::create([
                'borrower_id' => $user->id,
                'processed_by' => auth()->id(),
                'purpose' => $request->purpose,
                'borrow_date' => now(),
                'expected_return_date' => $request->expected_return_date,
                'status' => 'active',
                'notes' => $request->notes,
            ]);

            // Create borrowing items and update asset status
            foreach ($assets as $asset) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'borrowable_asset_id' => $asset->id,
                    'condition_on_borrow' => 'Good',
                ]);

                // Update asset status to in_use
                $asset->update(['status' => 'in_use']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assets borrowed successfully!',
                'borrower_name' => $user->name,
                'borrower_id' => $user->employee_id ?? $user->rfid_number,
                'asset_count' => $assets->count(),
                'expected_return' => Carbon::parse($request->expected_return_date)->format('M d, Y'),
                'borrowing_id' => $borrowing->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registerAndBorrow(Request $request)
    {
        try {
            $request->validate([
                'rfid_number' => 'required|string|unique:users,rfid_number',
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'asset_ids' => 'required|array|min:1',
                'asset_ids.*' => 'exists:borrowable_assets,id',
                'purpose' => 'required|string|max:255',
                'expected_return_date' => 'required|date|after_or_equal:today',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Create new user with RFID
            $user = User::create([
                'name' => $request->name,
                'username' => 'rfid_' . $request->rfid_number, // Generate username from RFID
                'rfid_number' => $request->rfid_number,
                'department' => $request->department,
                'role' => 'borrower',
                'password' => bcrypt(Str::random(16)), // Random password for RFID-only users
            ]);

            // Check if all selected assets are available
            $assets = BorrowableAsset::whereIn('id', $request->asset_ids)
                ->where('status', 'active')
                ->get();

            if ($assets->count() !== count($request->asset_ids)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected assets are no longer available.'
                ]);
            }

            // Create borrowing record
            $borrowing = Borrowing::create([
                'borrower_id' => $user->id,
                'processed_by' => auth()->id(),
                'purpose' => $request->purpose,
                'borrow_date' => now(),
                'expected_return_date' => $request->expected_return_date,
                'status' => 'active',
                'notes' => $request->notes,
            ]);

            // Create borrowing items and update asset status
            foreach ($assets as $asset) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'borrowable_asset_id' => $asset->id,
                    'condition_on_borrow' => 'Good',
                ]);

                $asset->update(['status' => 'in_use']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User registered and assets borrowed successfully!',
                'borrower_name' => $user->name,
                'borrower_id' => $user->rfid_number,
                'asset_count' => $assets->count(),
                'expected_return' => Carbon::parse($request->expected_return_date)->format('M d, Y'),
                'borrowing_id' => $borrowing->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available assets by category
     */
    public function getAssetsByCategory($categoryId)
    {
        $assets = BorrowableAsset::with(['category', 'location'])
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'assets' => $assets
        ]);
    }

    /**
     * Display available assets for borrowing
     */
    /**
     * Display all assets regardless of status
     */
    public function allAssets(Request $request)
    {
        $query = BorrowableAsset::with(['category', 'location']);

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->has('location') && $request->location != '') {
            $query->where('location_id', $request->location);
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('name')->paginate(15);
        
        // Get filter options
        $categories = DB::table('categories')->orderBy('name')->get();
        $locations = DB::table('locations')->orderBy('building')->orderBy('floor')->orderBy('room_number')->get();

        return view('borrowing.assets.all', compact('assets', 'categories', 'locations'));
    }

    /**
     * Display available assets
     */
    public function availableAssets(Request $request)
    {
        $query = BorrowableAsset::with(['category', 'location'])
            ->where('status', 'active');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->has('location') && $request->location != '') {
            $query->where('location_id', $request->location);
        }

        $assets = $query->orderBy('name')->paginate(15);
        
        // Get filter options
        $categories = DB::table('categories')->orderBy('name')->get();
        $locations = DB::table('locations')->orderBy('building')->orderBy('floor')->orderBy('room_number')->get();

        return view('borrowing.assets.available', compact('assets', 'categories', 'locations'));
    }

    /**
     * Display currently borrowed assets
     */
    public function borrowedAssets(Request $request)
    {
        $query = BorrowingItem::with(['borrowableAsset.category', 'borrowableAsset.location', 'borrowing.borrower'])
            ->whereHas('borrowing', function($q) {
                $q->whereIn('status', ['active', 'approved']);
            });

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('borrowableAsset', function($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('serial_number', 'like', "%{$search}%");
                })
                ->orWhereHas('borrowing.borrower', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('id_number', 'like', "%{$search}%");
                });
            });
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('borrowableAsset', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        $borrowedItems = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('borrowing.assets.borrowed', compact('borrowedItems', 'categories'));
    }

    /**
     * Show form to create a new borrowing asset
     */
    public function createAsset()
    {
        // Get categories and locations for dropdowns
        $categories = DB::table('categories')->orderBy('name')->get();
        $locations = DB::table('locations')
            ->orderBy('building')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('borrowing.assets.create', compact('categories', 'locations'));
    }

    /**
     * Store a new borrowing asset
     */
    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:borrowable_assets,serial_number',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'model' => 'nullable|string|max:255',
            'specification' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('assets', 'public');
        }

        // Create the asset
        $asset = BorrowableAsset::create([
            'name' => $validated['name'],
            'serial_number' => $validated['serial_number'],
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'model' => $validated['model'] ?? null,
            'specification' => $validated['specification'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? now(),
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active', // Set as active for borrowing
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('borrowing.assets.available')
            ->with('success','Asset added successfully and is now available for borrowing!');
    }

    /**
     * Handle asset return
     */
    public function returnAsset(Request $request)
    {
        try {
            $request->validate([
                'borrowing_id' => 'required|exists:borrowings,id',
                'condition' => 'required|string|in:Good,Fair,Damaged',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $borrowing = Borrowing::findOrFail($request->borrowing_id);

            // Update borrowing status
            $borrowing->update([
                'status' => 'returned',
                'actual_return_date' => now(),
                'return_notes' => $request->notes,
                'returned_by' => auth()->id(),
            ]);

            // Update all assets in this borrowing to active status
            foreach ($borrowing->items as $item) {
                $item->update([
                    'condition_on_return' => $request->condition,
                ]);

                $item->borrowableAsset->update([
                    'status' => 'active',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset returned successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark asset as missing
     */
    public function markAsMissing(Request $request)
    {
        try {
            $request->validate([
                'borrowing_id' => 'required|exists:borrowings,id',
                'notes' => 'required|string',
            ]);

            DB::beginTransaction();

            $borrowing = Borrowing::findOrFail($request->borrowing_id);

            // Update borrowing status
            $borrowing->update([
                'status' => 'missing',
                'return_notes' => 'MISSING: ' . $request->notes,
            ]);

            // Update all assets in this borrowing to missing status
            foreach ($borrowing->items as $item) {
                $item->borrowableAsset->update([
                    'status' => 'missing',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset marked as missing successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show borrowing history (returned and missing)
     */
    public function history(Request $request)
    {
        $query = Borrowing::with(['borrower', 'items.asset.category'])
            ->whereIn('status', ['returned', 'missing', 'lost', 'cancelled']);

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('borrower', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('items.asset', function($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('serial_number', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $borrowings = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('borrowing.history', compact('borrowings'));
    }

    /**
     * Generate reports grouped by user
     */
    public function reportsByUser(Request $request)
    {
        $query = User::withCount([
            'borrowings',
            'borrowings as active_borrowings_count' => function($q) {
                $q->where('status', 'active');
            },
            'borrowings as returned_borrowings_count' => function($q) {
                $q->where('status', 'returned');
            }
        ])->has('borrowings');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('rfid_number', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->has('department') && $request->department != '') {
            $query->where('department', $request->department);
        }

        $users = $query->orderBy('name')->paginate(15);

        // Get unique departments for filter
        $departments = User::has('borrowings')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();

        return view('borrowing.reports.by-user', compact('users', 'departments'));
    }

    /**
     * Generate reports grouped by asset
     */
    public function reportsByAsset(Request $request)
    {
        $query = BorrowableAsset::withCount([
            'borrowingItems',
            'borrowingItems as active_borrowings_count' => function($q) {
                $q->whereHas('borrowing', function($bq) {
                    $bq->where('status', 'active');
                });
            },
            'borrowingItems as returned_borrowings_count' => function($q) {
                $q->whereHas('borrowing', function($bq) {
                    $bq->where('status', 'returned');
                });
            }
        ])->with(['category', 'location'])
        ->has('borrowingItems');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('name')->paginate(15);

        // Get filter options
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('borrowing.reports.by-asset', compact('assets', 'categories'));
    }

    /**
     * Show detailed borrowing history for a specific user
     */
    public function userBorrowingDetail(User $user)
    {
        $borrowings = $user->borrowings()
            ->with(['items.borrowableAsset.category', 'processedBy'])
            ->orderBy('borrow_date', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $user->borrowings()->count(),
            'active' => $user->borrowings()->where('status', 'active')->count(),
            'returned' => $user->borrowings()->where('status', 'returned')->count(),
            'missing' => $user->borrowings()->where('status', 'missing')->count(),
        ];

        return view('borrowing.reports.user-detail', compact('user', 'borrowings', 'stats'));
    }

    /**
     * Show detailed borrowing history for a specific asset
     */
    public function assetBorrowingDetail(BorrowableAsset $asset)
    {
        $borrowingItems = $asset->borrowingItems()
            ->with(['borrowing.borrower', 'borrowing.processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $asset->borrowingItems()->count(),
            'active' => $asset->borrowingItems()->whereHas('borrowing', function($q) {
                $q->where('status', 'active');
            })->count(),
            'returned' => $asset->borrowingItems()->whereHas('borrowing', function($q) {
                $q->where('status', 'returned');
            })->count(),
            'missing' => $asset->borrowingItems()->whereHas('borrowing', function($q) {
                $q->where('status', 'missing');
            })->count(),
        ];

        return view('borrowing.reports.asset-detail', compact('asset', 'borrowingItems', 'stats'));
    }
}
