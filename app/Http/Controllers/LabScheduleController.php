<?php

namespace App\Http\Controllers;

use App\Models\LabSchedule;
use App\Models\User;
use App\Models\LabLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LabScheduleController extends Controller
{
    public function handleRfidAttendance(Request $request)
    {
        try {

            // Validate request (purpose required only for time-in)
            $request->validate([
                'rfid_number' => 'required|string',
                'laboratory' => 'required|string'
            ]);

            // Find user by RFID number
            $user = User::where('rfid_number', $request->rfid_number)
                ->where(function ($query) {
                    $query->where('position', 'Teacher')
                        ->orWhere('position', 'Faculty');
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid RFID card or unauthorized user'
                ]);
            }
            // Safety cooldown: prevent any tap within 2 minutes (120 seconds) of the user's last action
            $recentLog = LabLog::where('user_id', $user->id)
                ->orderByRaw('COALESCE(time_out, time_in) DESC')
                ->first();

            if ($recentLog) {
                $lastActionAt = $recentLog->time_out ?: $recentLog->time_in;
                if ($lastActionAt) {
                    // Signed elapsed seconds since last action; negative if lastActionAt is in the future
                    $elapsedSeconds = Carbon::parse($lastActionAt)->diffInSeconds(now(), false);
                    if ($elapsedSeconds < 120) {
                        $remainingSeconds = 120 - max(0, $elapsedSeconds);
                        $remainingMinutes = (int) ceil($remainingSeconds / 60);
                        return response()->json([
                            'success' => false,
                            'message' => "Please wait {$remainingMinutes} minute(s) before tapping again."
                        ], 429);
                    }
                }
            }

            // Check if user has any ongoing session in any laboratory
            $anyOngoingLog = LabLog::where('user_id', $user->id)
                ->where('status', 'on-going')
                ->latest('time_in')
                ->first();

            // Get the latest ongoing session in the current laboratory if exists
            $ongoingLog = LabLog::where('user_id', $user->id)
                ->where('laboratory', $request->laboratory)
                ->where('status', 'on-going')
                ->latest('time_in')
                ->first();

            // If there's an ongoing session in the current laboratory by this user
            if ($ongoingLog) {
                // This is a time-out action
                $ongoingLog->update([
                    'time_out' => now(),
                    'status' => 'completed'
                ]);

                return response()->json([
                    'success' => true,
                    'faculty_name' => $user->name,
                    'faculty_id' => $user->id,
                    'status' => 'completed',
                    'time_in' => $ongoingLog->time_in,
                    'time_out' => $ongoingLog->time_out,
                    'message' => 'Time Out recorded successfully'
                ]);
            }
            
            // If there's an ongoing session in another laboratory for this user
            $hasOngoingInOtherLab = $anyOngoingLog && $anyOngoingLog->laboratory != $request->laboratory;

            // Prevent time-in if another user currently occupies the lab
            $labOngoingByOther = LabLog::where('laboratory', $request->laboratory)
                ->where('status', 'on-going')
                ->where('user_id', '!=', $user->id)
                ->first();
            if ($labOngoingByOther) {
                return response()->json([
                    'success' => false,
                    'message' => 'This laboratory is currently unavailable. Only the same user who tapped in can tap out.'
                ], 403);
            }

            // This is a time-in action (require purpose here)
            if (!$request->filled('purposes') && !$request->filled('purpose')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purpose is required for time-in'
                ], 422);
            }
            
            // Handle both 'purposes' (array) and 'purpose' (string) for backward compatibility
            $purposeValue = $request->purposes 
                ? (is_array($request->purposes) ? implode(', ', $request->purposes) : $request->purposes)
                : $request->purpose;
            
            $labLog = new LabLog([
                'user_id' => $user->id,
                'laboratory' => $request->laboratory,
                'purpose' => $purposeValue,
                'time_in' => now(),
                'status' => 'on-going'
            ]);

            if (!$labLog->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save attendance record'
                ], 500);
            }

            // Prepare response with ongoing session warning if applicable
            $response = [
                'success' => true,
                'faculty_name' => $user->name,
                'faculty_id' => $user->id,
                'status' => $labLog->status,
                'time_in' => $labLog->time_in,
                'message' => 'Time In recorded successfully'
            ];

            // Add ongoing session warning if user has ANY ongoing session (from previous days)
            if ($anyOngoingLog) {
                // Check if the ongoing session is from a previous day
                $sessionDate = $anyOngoingLog->time_in->format('Y-m-d');
                $today = now()->format('Y-m-d');
                
                if ($sessionDate < $today) {
                    $response['warning'] = [
                        'has_ongoing_session' => true,
                        'ongoing_laboratory' => $anyOngoingLog->laboratory,
                        'ongoing_time_in' => $anyOngoingLog->time_in,
                        'ongoing_purpose' => $anyOngoingLog->purpose,
                        'message' => "⚠️ You have an ongoing session in Laboratory {$anyOngoingLog->laboratory} since {$anyOngoingLog->time_in->format('M d, Y h:i A')}. Please notify the admin about your actual logout time for that session."
                    ];
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error processing RFID attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLabLogs(Request $request)
    {
        $query = LabLog::with('user')
            ->when($request->laboratory, function ($q) use ($request) {
                return $q->where('laboratory', $request->laboratory);
            })
            ->when($request->date, function ($q) use ($request) {
                return $q->whereDate('time_in', $request->date);
            })
            ->orderBy('time_in', 'desc');

        $logs = $query->get();

        return response()->json($logs);
    }

    public function previewPDF(Request $request)
    {
        try {
            $query = LabLog::with('user')
                ->when($request->laboratory, function ($q) use ($request) {
                    return $q->where('laboratory', $request->laboratory);
                })
                ->when($request->purpose, function ($q) use ($request) {
                    return $q->where('purpose', $request->purpose);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->when($request->time_in_start_date, function ($q) use ($request) {
                    return $q->whereDate('time_in', '>=', $request->time_in_start_date);
                })
                ->when($request->time_in_end_date, function ($q) use ($request) {
                    return $q->whereDate('time_in', '<=', $request->time_in_end_date);
                })
                ->when($request->time_out_start_date, function ($q) use ($request) {
                    return $q->whereDate('time_out', '>=', $request->time_out_start_date);
                })
                ->when($request->time_out_end_date, function ($q) use ($request) {
                    return $q->whereDate('time_out', '<=', $request->time_out_end_date);
                })
                ->orderBy('time_in', 'desc');

            $logs = $query->get();

            // Prepare filters data for the template
            $filters = [
                'laboratory' => $request->laboratory,
                'purpose' => $request->purpose,
                'status' => $request->status,
                'start_date' => $request->time_in_start_date,
                'end_date' => $request->time_in_end_date,
            ];

            // Generate PDF and stream it (for preview)
            $pdf = PDF::loadView('exports.lab-attendance-history-pdf', compact('logs'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('lab-attendance-history-preview.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to preview PDF: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $query = LabLog::with('user')
                ->when($request->laboratory, function ($q) use ($request) {
                    return $q->where('laboratory', $request->laboratory);
                })
                ->when($request->has('purpose') && is_array($request->purpose), function ($q) use ($request) {
                    return $q->whereIn('purpose', $request->purpose);
                })
                ->when($request->purpose && !is_array($request->purpose), function ($q) use ($request) {
                    return $q->where('purpose', $request->purpose);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->when($request->time_in_start_date, function ($q) use ($request) {
                    return $q->whereDate('time_in', '>=', $request->time_in_start_date);
                })
                ->when($request->time_in_end_date, function ($q) use ($request) {
                    return $q->whereDate('time_in', '<=', $request->time_in_end_date);
                })
                ->when($request->time_out_start_date, function ($q) use ($request) {
                    return $q->whereDate('time_out', '>=', $request->time_out_start_date);
                })
                ->when($request->time_out_end_date, function ($q) use ($request) {
                    return $q->whereDate('time_out', '<=', $request->time_out_end_date);
                })
                ->orderBy('time_in', 'desc');

            $logs = $query->get();

            // Process signatures if provided
            $signatures = [];
            if ($request->has('signatures')) {
                $signaturesData = json_decode($request->signatures, true);
                if (is_array($signaturesData)) {
                    foreach ($signaturesData as $signature) {
                        if (isset($signature['label'], $signature['name'], $signature['signature'])) {
                            $signatures[] = [
                                'label' => $signature['label'],
                                'name' => $signature['name'],
                                'signature_base64' => $signature['signature']
                            ];
                        }
                    }
                }
            }

            // Prepare filters data for the template
            $filters = [
                'laboratory' => $request->laboratory,
                'purpose' => is_array($request->purpose) ? implode(', ', $request->purpose) : $request->purpose,
                'status' => $request->status,
                'start_date' => $request->time_in_start_date,
                'end_date' => $request->time_in_end_date,
            ];

            // Generate PDF and stream it
            $pdf = PDF::loadView('exports.lab-attendance-history-pdf', compact('logs', 'signatures'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('lab-attendance-history-report.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function checkAvailability($laboratory)
    {
        $ongoingLog = LabLog::where('laboratory', $laboratory)
            ->where('status', 'on-going')
            ->first();

        return response()->json([
            'status' => $ongoingLog ? 'ongoing' : 'available'
        ]);
    }

    public function getAllLabsStatus()
    {
        $labs = \App\Models\Laboratory::orderBy('number')->pluck('number')->toArray();
        $statuses = [];

        foreach ($labs as $lab) {
            $ongoingLog = LabLog::where('laboratory', $lab)
                ->where('status', 'on-going')
                ->first();

            $statuses[] = [
                'laboratory' => $lab,
                'status' => $ongoingLog ? 'on-going' : 'available',
                'session_date' => $ongoingLog ? $ongoingLog->time_in->format('Y-m-d') : null
            ];
        }

        return response()->json($statuses);
    }

    // Public endpoint: returns whether the CURRENT authenticated user has an ongoing session.
    // If unauthenticated (e.g., public RFID logging), returns false so the UI can proceed.
    public function checkUserOngoingSession()
    {
        try {
            if (!auth()->check()) {
                return response()->json(['hasOngoingSession' => false]);
            }

            $has = LabLog::where('user_id', auth()->id())
                ->where('status', 'on-going')
                ->exists();

            return response()->json(['hasOngoingSession' => $has]);
        } catch (\Exception $e) {
            return response()->json(['hasOngoingSession' => false]);
        }
    }

    public function logging()
    {
        $laboratories = \App\Models\Laboratory::orderBy('number')->get();
        
        return view('lab-schedule.logging', [
            'laboratories' => $laboratories
        ]);
    }

    // Manual logout tools
    public function manualLogoutPage(Request $request)
    {
        // Basic guard: only authenticated users or admins should access; adjust as needed
        // if (!auth()->check()) { return redirect()->route('login'); }

        $query = LabLog::with('user')
            ->where('status', 'on-going')
            ->orderBy('time_in', 'desc');

        // Optional filters
        if ($request->laboratory) {
            $query->where('laboratory', $request->laboratory);
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $ongoingLogs = $query->paginate(10)->withQueryString();

        // Get recent manual logins (created today)
        $recentManualLogins = LabLog::with('user')
            ->whereDate('created_at', Carbon::today())
            ->whereNotNull('notes') // Assuming manual logins have notes
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'manual_page');

        $laboratories = \App\Models\Laboratory::orderBy('number')->pluck('number');

        return view('lab-schedule.manual-logout', [
            'ongoingLogs' => $ongoingLogs,
            'recentManualLogins' => $recentManualLogins,
            'laboratories' => $laboratories
        ]);
    }

    public function manualLogoutSubmit(Request $request)
    {
        $request->validate([
            'log_id' => 'required|exists:lab_logs,id',
            'time_out' => 'required|date'
        ]);

        $log = LabLog::with('user')->findOrFail($request->log_id);

        // Ensure time_out is after time_in
        $proposedTimeOut = now()->parse($request->time_out);
        if ($proposedTimeOut->lessThanOrEqualTo($log->time_in)) {
            return back()->withErrors(['time_out' => 'Time out must be after time in.'])->withInput();
        }

        $log->time_out = $proposedTimeOut;
        $log->status = 'completed';
        $log->save();

        return redirect()->route('lab.manualLogout')->with('status', 'Manual logout recorded for '.$log->user->name.' in Laboratory '.$log->laboratory.'.');
    }

    public function manualLoginSubmit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'laboratory' => 'required|string',
            'time_in' => 'required|date',
            'purpose' => 'required|array|min:1',
            'purpose.*' => 'in:teaching,research,personal,other',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if user exists and is faculty/teacher
        $user = User::where('id', $request->user_id)
            ->where(function ($query) {
                $query->where('position', 'Teacher')
                    ->orWhere('position', 'Faculty');
            })
            ->first();

        if (!$user) {
            return back()->withErrors(['user_id' => 'User not found or not authorized to use laboratories.'])->withInput();
        }

        // Check if laboratory exists
        $laboratory = \App\Models\Laboratory::where('number', $request->laboratory)->first();
        if (!$laboratory) {
            return back()->withErrors(['laboratory' => 'Laboratory not found.'])->withInput();
        }

        // Check if laboratory is available (no ongoing sessions at the requested time)
        $timeIn = Carbon::parse($request->time_in);
        $ongoingInLab = LabLog::where('laboratory', $request->laboratory)
            ->where('status', 'on-going')
            ->where(function ($query) use ($timeIn) {
                $query->where('time_in', '<=', $timeIn);
            })
            ->exists();

        if ($ongoingInLab) {
            return back()->withErrors(['laboratory' => 'Laboratory ' . $request->laboratory . ' is currently unavailable. There is an ongoing session.'])->withInput();
        }

        // Check if user already has an ongoing session in any lab
        $existingSession = LabLog::where('user_id', $request->user_id)
            ->where('status', 'on-going')
            ->first();

        if ($existingSession) {
            return back()->withErrors(['user_id' => 'User already has an ongoing session in Laboratory '.$existingSession->laboratory.'. Please log out first.'])->withInput();
        }

        // Validate time_in is not in the future
        if ($timeIn->isFuture()) {
            return back()->withErrors(['time_in' => 'Time in cannot be in the future.'])->withInput();
        }

        // Create manual login with multiple purposes
        $purposes = implode(', ', $request->purpose);
        
        $log = new LabLog();
        $log->user_id = $request->user_id;
        $log->laboratory = $request->laboratory;
        $log->time_in = $timeIn;
        $log->purpose = $purposes;
        $log->status = 'on-going';
        $log->notes = $request->notes ? 'Manual Login: ' . $request->notes : 'Manual Login';
        $log->save();

        return redirect()->route('lab.manualLogout')->with('status', 'Manual log-in created for '.$user->name.' in Laboratory '.$request->laboratory.'.');
    }

    public function searchFaculty(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }

        // Search by ID, name, or username
        $users = User::where(function ($q) use ($query) {
                $q->where('id', 'like', '%' . $query . '%')
                  ->orWhere('name', 'like', '%' . $query . '%')
                  ->orWhere('username', 'like', '%' . $query . '%');
            })
            ->where(function ($q) {
                $q->where('position', 'Teacher')
                  ->orWhere('position', 'Faculty');
            })
            ->select('id', 'name', 'username', 'position', 'department')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function history(Request $request)
    {
        $laboratories = \App\Models\Laboratory::orderBy('number')->pluck('number');

        $query = LabLog::with('user')
            ->when($request->laboratory, function ($q) use ($request) {
                return $q->where('laboratory', $request->laboratory);
            })
            ->when($request->purpose, function ($q) use ($request) {
                return $q->where('purpose', $request->purpose);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->time_in_start_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '>=', $request->time_in_start_date);
            })
            ->when($request->time_in_end_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '<=', $request->time_in_end_date);
            })
            ->when($request->time_out_start_date, function ($q) use ($request) {
                return $q->whereDate('time_out', '>=', $request->time_out_start_date);
            })
            ->when($request->time_out_end_date, function ($q) use ($request) {
                return $q->whereDate('time_out', '<=', $request->time_out_end_date);
            })
            ->orderBy('time_in', 'desc');

        $logs = $query->paginate(10)->withQueryString();

        return view('lab-schedule.history', [
            'logs' => $logs,
            'laboratories' => $laboratories
        ]);
    }

    // Add this method to handle multiple deletions
    public function destroyMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No records selected for deletion'
                ]);
            }
            
            LabLog::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' record(s) deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ]);
        }
    }
}
