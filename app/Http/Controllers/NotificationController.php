<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RepairRequest;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($notifications);
    }

    public function all(Request $request)
    {
        $query = auth()->user()->notifications();

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply date filter
        if ($request->filled('date')) {
            $now = Carbon::now();
            
            switch ($request->date) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->where('created_at', '>=', $now->copy()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', $now->copy()->subMonth());
                    break;
                case 'quarter':
                    $query->where('created_at', '>=', $now->copy()->subMonths(3));
                    break;
            }
        }

        // Get counts for statistics
        $totalCount = auth()->user()->notifications()->count();
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        $readCount = auth()->user()->notifications()->where('is_read', true)->count();

        // Apply filtered counts if filters are active
        if ($request->filled('status') || $request->filled('type') || $request->filled('date')) {
            $filteredQuery = clone $query;
            $filteredTotal = $filteredQuery->count();
            $filteredUnread = $filteredQuery->where('is_read', false)->count();
            $filteredRead = $filteredQuery->where('is_read', true)->count();
        } else {
            $filteredTotal = $totalCount;
            $filteredUnread = $unreadCount;
            $filteredRead = $readCount;
        }

        // Ensure we have valid counts
        $filteredTotal = max(0, $filteredTotal);
        $filteredUnread = max(0, $filteredUnread);
        $filteredRead = max(0, $filteredRead);

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Append filter parameters to pagination links
        $notifications->appends($request->only(['status', 'type', 'date']));

        return view('notifications.all', compact(
            'notifications', 
            'totalCount', 
            'unreadCount', 
            'readCount',
            'filteredTotal',
            'filteredUnread',
            'filteredRead'
        ));
    }

    public function redirect($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Only handle repair notifications here
        $typeBase = strtolower(explode('_', $notification->type)[0] ?? '');
        if ($typeBase !== 'repair' && strpos(strtolower($notification->message), 'repair') === false) {
            // Fallback to original link if present
            if ($notification->link) {
                if (!$notification->is_read) {
                    $notification->update(['is_read' => true]);
                }
                return redirect($notification->link);
            }
            // As a last resort, go to repair status page
            return redirect()->route('repair.status');
        }

        // Extract the ticket number from the message (e.g., ABC-1234)
        $ticket = null;
        if (preg_match('/([A-Z0-9\-]{6,})/', $notification->message, $m)) {
            $ticket = $m[1];
        }
        // Fallback from link query if provided
        if (!$ticket && $notification->link) {
            $parts = parse_url($notification->link);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $qs);
                $ticket = $qs['ticket'] ?? null;
            }
        }

        if ($ticket) {
            $repair = RepairRequest::where('ticket_number', $ticket)->first();
            if ($repair) {
                if (!$notification->is_read) {
                    $notification->update(['is_read' => true]);
                }
                if ($repair->completed_at || $repair->status === 'completed') {
                    return redirect()->route('repair.show', ['id' => $repair->id]);
                }
                if (\Route::has('repair.details')) {
                    return redirect()->route('repair.details', ['id' => $repair->id]);
                }
                return redirect()->route('repair.status', ['search' => $ticket]);
            }
        }

        // Default fallback behavior
        if ($notification->link) {
            if (!$notification->is_read) {
                $notification->update(['is_read' => true]);
            }
            return redirect($notification->link);
        }
        return redirect()->route('repair.status');
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 403);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}