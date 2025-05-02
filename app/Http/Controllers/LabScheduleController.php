<?php

namespace App\Http\Controllers;

use App\Models\LabSchedule;
use App\Models\User;
use App\Models\LabLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabScheduleController extends Controller
{
    public function index()
    {
        $faculty = User::where('position', 'Faculty')
            ->where('status', 'Active')
            ->get();

        $coordinators = User::where('group_id', '4')
            ->where('status', 'Active')
            ->get();

        return view('lab-schedule.LabSchedule', [
            'laboratories' => [
                'Laboratory 401',
                'Laboratory 402',
                'Laboratory 403',
                'Laboratory 404',
                'Laboratory 405',
                'Laboratory 406'
            ],
            'departments' => [
                'Grade School',
                'Elementary',
                'Junior High School',
                'Senior High School',
                'College'
            ],
            'faculty' => $faculty,
            'coordinators' => $coordinators  // Changed from 'coordinator' to 'coordinators'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'laboratory' => 'required|string',
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'collaborator_id' => 'required|exists:users,id',
                'department' => 'required|string',
                'subject_course' => 'required|string',
                'professor_id' => 'required|exists:users,id'
            ]);

            // Validate time range (5 AM to 11 PM)
            $startTime = new \DateTime($validated['start']);
            $endTime = new \DateTime($validated['end']);

            $startHour = (int)$startTime->format('H');
            $endHour = (int)$endTime->format('H');

            if ($startHour < 5 || $startHour >= 23) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start time must be between 5:00 AM and 11:00 PM'
                ], 422);
            }

            if ($endHour < 5 || $endHour > 23) {
                return response()->json([
                    'success' => false,
                    'message' => 'End time must be between 5:00 AM and 11:00 PM'
                ], 422);
            }

            // Check for schedule conflicts
            $conflict = LabSchedule::where('laboratory', $validated['laboratory'])
                ->where(function ($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->where('start', '<', $validated['end'])
                            ->where('end', '>', $validated['start']);
                    });
                })
                ->where(function($query) {
                    $query->where('status', '!=', 'Completed')
                        ->orWhereNull('status');
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a schedule for this laboratory during the selected time period.'
                ], 422);
            }

            // Get professor name from id
            $professor = User::findOrFail($validated['professor_id']);
            $validated['professor'] = $professor->name;
            unset($validated['professor_id']); // Remove professor_id from validated data

            $schedule = LabSchedule::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Schedule logged successfully.',
                'data' => $schedule
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEvents()
    {
        $user = auth()->user();

        // If user is Admin (group_id = 1) or Secretary (group_id = 2), show all schedules
        // regardless of their position
        if ($user->group_id <= 2) {
            $events = LabSchedule::where('status', '!=', 'Completed')
                ->orWhereNull('status')
                ->get();
        } else {
            // For other users who are Faculty, only show their schedules
            $events = LabSchedule::where('professor', $user->name)
                ->where(function($query) {
                    $query->where('status', '!=', 'Completed')
                        ->orWhereNull('status');
                })
                ->get();
        }

        return response()->json($events->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'start' => $schedule->start,
                'end' => $schedule->end,
                'laboratory' => $schedule->laboratory,
                'department' => $schedule->department,
                'subject_course' => $schedule->subject_course,
                'professor' => $schedule->professor,
                'professor_id' => $schedule->professor_id
            ];
        }));
    }

    public function history()
    {
        $labHistory = LabSchedule::orderBy('start', 'desc')
            ->get();

        return view('lab-schedule.history', compact('labHistory'));
    }
    public function destroy(Request $request)
    {
        try {
            LabSchedule::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting schedules'], 500);
        }
    }

    public function logging()
    {
        return view('lab-schedule.logging', [
            'laboratories' => [
                'Laboratory 401',
                'Laboratory 402',
                'Laboratory 403',
                'Laboratory 404',
                'Laboratory 405',
                'Laboratory 406'
            ]
        ]);
    }

    public function getScheduleByRfid(Request $request)
    {
        try {
            // Find user by RFID number
            $user = User::where('rfid_number', $request->rfid)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid RFID card.'
                ]);
            }

            // Get current date and time
            $now = now();

            // Find the schedule for this user, including "On Going" schedules
            $schedule = LabSchedule::where('professor', $user->name)
                ->whereDate('start', $now->toDateString())
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', 'On Going');
                })
                ->first();

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'No schedule found for today.'
                ]);
            }

            // Add status to the response
            return response()->json([
                'success' => true,
                'schedule' => [
                    'id' => $schedule->id,
                    'laboratory' => $schedule->laboratory,
                    'date' => $now->toDateString(),
                    'time_in' => Carbon::parse($schedule->start)->format('H:i'),
                    'time_out' => Carbon::parse($schedule->end)->format('H:i'),
                    'professor' => $user->name,
                    'subject_course' => $schedule->subject_course,
                    'status' => $schedule->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    public function submitLog(Request $request)
    {
        try {
            $validated = $request->validate([
                'laboratory' => 'required|string',
                'date' => 'required|date',
                'time_in' => 'required',
                'time_out' => 'required',
                'professor_name' => 'required|string',
                'subject_course' => 'required|string',
                'schedule_id' => 'required|exists:lab_schedules,id'
            ]);

            $user = User::where('name', $validated['professor_name'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Professor not found.'
                ], 404);
            }

            // Update schedule status
            $schedule = LabSchedule::find($validated['schedule_id']);
            if (!$schedule->status || $schedule->status === 'Scheduled') {
                $schedule->status = 'On Going';
            } else if ($schedule->status === 'On Going') {
                $schedule->status = 'Completed';
            }
            $schedule->save();

            $log = LabLog::create([
                'laboratory' => $validated['laboratory'],
                'date' => $validated['date'],
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'],
                'professor_name' => $validated['professor_name'],
                'subject_course' => $validated['subject_course'],
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lab log created successfully.',
                'data' => $log,
                'status' => $schedule->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the log: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'laboratory' => 'required|string',
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'collaborator_id' => 'required|exists:users,id',
                'department' => 'required|string',
                'subject_course' => 'required|string',
                'professor_id' => 'required|exists:users,id'
            ]);

            $schedule = LabSchedule::findOrFail($id);

            // Check for schedule conflicts excluding the current schedule
            $conflict = LabSchedule::where('laboratory', $validated['laboratory'])
                ->where('id', '!=', $id)
                ->where(function ($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->where('start', '<', $validated['end'])
                            ->where('end', '>', $validated['start']);
                    });
                })
                ->where(function($query) {
                    $query->where('status', '!=', 'Completed')
                        ->orWhereNull('status');
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a schedule for this laboratory during the selected time period.'
                ], 422);
            }

            // Get professor name from id
            $professor = User::findOrFail($validated['professor_id']);
            $validated['professor'] = $professor->name;
            unset($validated['professor_id']); // Remove professor_id from validated data

            $schedule->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully.',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the schedule: ' . $e->getMessage()
            ], 500);
        }
    }
}
