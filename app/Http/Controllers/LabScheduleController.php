<?php

namespace App\Http\Controllers;

use App\Models\LabSchedule;
use App\Models\User;
use App\Models\LabLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LabScheduleController extends Controller
{
    public function handleRfidAttendance(Request $request)
    {
        try {

            // Validate request
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
                ], 401);
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

            // If there's an ongoing session in the current laboratory
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
            
            // If there's an ongoing session in another laboratory
            if ($anyOngoingLog && $anyOngoingLog->laboratory != $request->laboratory) {
                return response()->json([
                    'success' => false,
                    'message' => "You have an ongoing session in Laboratory {$anyOngoingLog->laboratory}. Please complete that session first."
                ], 400);
            }

            // This is a time-in action
            $labLog = new LabLog([
                'user_id' => $user->id,
                'laboratory' => $request->laboratory,
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
        $labs = ['401', '402', '403', '404', '405', '406'];
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

    public function logging()
    {
        $laboratories = ['401', '402', '403', '404', '405', '406'];
        
        return view('lab-schedule.logging', [
            'laboratories' => $laboratories
        ]);
    }

    // Add this method to match the route used in history.blade.php
    public function history(Request $request)
    {
        $laboratories = ['401', '402', '403', '404', '405', '406'];

        $query = LabLog::with('user')
            ->when($request->laboratory, function ($q) use ($request) {
                return $q->where('laboratory', $request->laboratory);
            })
            ->when($request->date, function ($q) use ($request) {
                return $q->whereDate('time_in', $request->date);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->orderBy('time_in', 'desc');

        $logs = $query->get();

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
