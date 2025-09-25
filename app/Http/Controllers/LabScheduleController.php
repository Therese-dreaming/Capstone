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
            // Safety cooldown: prevent any tap within 5 minutes (300 seconds) of the user's last action
            $recentLog = LabLog::where('user_id', $user->id)
                ->orderByRaw('COALESCE(time_out, time_in) DESC')
                ->first();

            if ($recentLog) {
                $lastActionAt = $recentLog->time_out ?: $recentLog->time_in;
                if ($lastActionAt) {
                    // Signed elapsed seconds since last action; negative if lastActionAt is in the future
                    $elapsedSeconds = Carbon::parse($lastActionAt)->diffInSeconds(now(), false);
                    if ($elapsedSeconds < 300) {
                        $remainingSeconds = 300 - max(0, $elapsedSeconds);
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
            if ($anyOngoingLog && $anyOngoingLog->laboratory != $request->laboratory) {
                return response()->json([
                    'success' => false,
                    'message' => "You have an ongoing session in Laboratory {$anyOngoingLog->laboratory}. Please complete that session first."
                ], 400);
            }

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
            if (!$request->filled('purpose')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purpose is required for time in.'
                ], 422);
            }
            $labLog = new LabLog([
                'user_id' => $user->id,
                'laboratory' => $request->laboratory,
                'purpose' => $request->purpose,
                'time_in' => now(),
                'status' => 'on-going'
            ]);

            if (!$labLog->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save attendance record'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'faculty_name' => $user->name,
                'faculty_id' => $user->id,
                'status' => $labLog->status,
                'time_in' => $labLog->time_in,
                'message' => 'Time In recorded successfully'
            ]);
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
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '<=', $request->end_date);
            })
            ->orderBy('time_in', 'desc');

        $logs = $query->get();

        $data = [
            'logs' => $logs,
            'filters' => [
                'laboratory' => $request->laboratory,
                'purpose' => $request->purpose,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]
        ];

        return view('exports.lab-logs-pdf', $data)->render();
    }

    public function exportPDF(Request $request)
    {
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
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('time_in', '<=', $request->end_date);
            })
            ->orderBy('time_in', 'desc');

        $logs = $query->get();

        $pdf = PDF::loadView('exports.lab-logs-pdf', [
            'logs' => $logs,
            'filters' => [
                'laboratory' => $request->laboratory,
                'purpose' => $request->purpose,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]
        ]);

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="lab_attendance_history.pdf"');
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
                'status' => $ongoingLog ? 'on-going' : 'available'
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

        $laboratories = \App\Models\Laboratory::orderBy('number')->pluck('number');

        return view('lab-schedule.manual-logout', [
            'ongoingLogs' => $ongoingLogs,
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
