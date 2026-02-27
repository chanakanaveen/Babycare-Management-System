<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Moh;
use App\Models\VaccinationSchedule;
use Illuminate\Support\Facades\DB;

class VaccinationScheduleController extends Controller
{
    /**
     * Display all vaccination schedules (MOH master list).
     */
    public function index(Request $request)
    {
        try {
            $moh = null;
            if (Auth::guard('moh')->check()) {
                $moh = Moh::findOrFail(auth()->id());
            }

            $schedules = VaccinationSchedule::where('status', 1)->get();

            $data = [
                'pageTitle' => 'Vaccination Schedules',
                'schedules' => $schedules,
                'moh' => $moh,
            ];

            return view('back.pages.moh.vaccination-schedules', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store a new vaccination schedule.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'vaccine_name'          => 'required|string|max:255',
                'description'           => 'required|string',
                'recommended_age_months'=> 'required|integer|min:0',
                'doses_required'        => 'required|integer|min:1',
                'dose_schedule'         => 'nullable|json',
                'is_mandatory'          => 'nullable|boolean',
            ]);

            $schedule = VaccinationSchedule::create([
                'vaccine_name'           => $request->vaccine_name,
                'description'            => $request->description,
                'recommended_age_months' => $request->recommended_age_months,
                'doses_required'         => $request->doses_required,
                'dose_schedule'          => $request->dose_schedule ? json_decode($request->dose_schedule, true) : null,
                'is_mandatory'           => $request->has('is_mandatory') ? $request->is_mandatory : true,
                'status'                 => 1,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Vaccination schedule created successfully.',
                    'data'   => $schedule,
                ]);
            }

            return redirect()->back()->with('success', 'Vaccination schedule created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Error: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing vaccination schedule.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'vaccine_name'          => 'required|string|max:255',
                'description'           => 'required|string',
                'recommended_age_months'=> 'required|integer|min:0',
                'doses_required'        => 'required|integer|min:1',
                'dose_schedule'         => 'nullable|json',
                'is_mandatory'          => 'nullable|boolean',
            ]);

            $schedule = VaccinationSchedule::findOrFail($id);
            $schedule->update([
                'vaccine_name'           => $request->vaccine_name,
                'description'            => $request->description,
                'recommended_age_months' => $request->recommended_age_months,
                'doses_required'         => $request->doses_required,
                'dose_schedule'          => $request->dose_schedule ? json_decode($request->dose_schedule, true) : $schedule->dose_schedule,
                'is_mandatory'           => $request->has('is_mandatory') ? $request->is_mandatory : $schedule->is_mandatory,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Vaccination schedule updated successfully.',
                    'data'   => $schedule->fresh(),
                ]);
            }

            return redirect()->back()->with('success', 'Vaccination schedule updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Error: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Soft-delete a vaccination schedule (set status = 0).
     */
    public function destroy(Request $request, $id)
    {
        try {
            $schedule = VaccinationSchedule::findOrFail($id);
            $schedule->update(['status' => 0]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Vaccination schedule deleted successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Vaccination schedule deleted successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Error: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }
}
