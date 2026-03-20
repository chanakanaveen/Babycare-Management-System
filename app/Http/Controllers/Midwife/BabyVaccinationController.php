<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\BabyVaccination;
use App\Models\VaccinationSchedule;
use App\Models\Midwife;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BabyVaccinationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * List all vaccinations for a specific baby assigned to the authenticated midwife.
     */
    public function index(Request $request, $babyId)
    {
        try {
            $midwifeId = Auth::guard('midwife')->id();

            // Verify the baby is assigned to this midwife
            $baby = Baby::where('baby_id', $babyId)
                        ->where('midwife_id', $midwifeId)
                        ->first();

            if (!$baby) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'msg'    => 'Baby not found or not assigned to you.',
                    ], 403);
                }
                return redirect()->back()->with('fail', 'Baby not found or not assigned to you.');
            }

            $vaccinations = BabyVaccination::with('vaccine', 'midwife')
                ->where('baby_id', $babyId)
                ->orderBy('scheduled_date', 'asc')
                ->get();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Baby vaccinations retrieved successfully.',
                    'data'   => [
                        'baby'         => $baby,
                        'vaccinations' => $vaccinations,
                    ],
                ]);
            }

            $data = [
                'pageTitle'    => 'Baby Vaccinations - ' . $baby->full_name,
                'baby'         => $baby,
                'vaccinations' => $vaccinations,
            ];

            return view('back.pages.midwife.baby-vaccinations', $data);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Schedule a new vaccination for a baby.
     */
    public function schedule(Request $request)
    {
        try {
            $request->validate([
                'baby_id'        => 'required|exists:baby,baby_id',
                'vaccine_id'     => 'required|exists:vaccination_schedules,vaccine_id',
                'dose_number'    => 'required|integer|min:1',
                'scheduled_date' => 'required|date',
                'notes'          => 'nullable|string|max:500',
            ]);

            $midwifeId = Auth::guard('midwife')->id();

            // Verify the baby is assigned to this midwife
            $baby = Baby::where('baby_id', $request->baby_id)
                        ->where('midwife_id', $midwifeId)
                        ->first();

            if (!$baby) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Baby not found or not assigned to you.',
                ], 403);
            }

            $vaccination = BabyVaccination::create([
                'baby_id'             => $request->baby_id,
                'vaccine_id'          => $request->vaccine_id,
                'dose_number'         => $request->dose_number,
                'midwife_id'          => $midwifeId,
                'scheduled_date'      => $request->scheduled_date,
                'vaccination_status'  => 'scheduled',
                'notes'               => $request->notes,
                'reminder_sent'       => false,
            ]);

            // Trigger parent notification
            if ($vaccination->vaccination_status === 'scheduled' && $vaccination->scheduled_date) {
                $this->notificationService->notifyParentVaccineScheduled($vaccination);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Vaccination scheduled successfully.',
                    'data'   => $vaccination->load('vaccine'),
                ]);
            }

            return redirect()->back()->with('success', 'Vaccination scheduled successfully.');
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
                return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update a baby vaccination record (administer, mark as missed, update details).
     */
    public function update(Request $request, $recordId)
    {
        try {
            $request->validate([
                'vaccination_status' => 'required|in:scheduled,administered,missed,overdue',
                'administered_date'  => 'nullable|date',
                'batch_number'       => 'nullable|string|max:100',
                'next_dose_date'     => 'nullable|date',
                'notes'              => 'nullable|string|max:500',
            ]);

            $midwifeId = Auth::guard('midwife')->id();

            $vaccination = BabyVaccination::findOrFail($recordId);

            // Verify the baby is assigned to this midwife
            $baby = Baby::where('baby_id', $vaccination->baby_id)
                        ->where('midwife_id', $midwifeId)
                        ->first();

            if (!$baby) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'You are not authorized to update this vaccination record.',
                ], 403);
            }

            $vaccination->update([
                'vaccination_status' => $request->vaccination_status,
                'administered_date'  => $request->administered_date ?? $vaccination->administered_date,
                'batch_number'       => $request->batch_number ?? $vaccination->batch_number,
                'next_dose_date'     => $request->next_dose_date ?? $vaccination->next_dose_date,
                'midwife_id'         => $midwifeId,
                'notes'              => $request->notes ?? $vaccination->notes,
            ]);

            // Re-trigger notification if rescheduled
            if ($request->vaccination_status === 'scheduled' && $vaccination->scheduled_date && !$vaccination->parent_notified) {
                $this->notificationService->notifyParentVaccineScheduled($vaccination->fresh());
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Vaccination record updated successfully.',
                    'data'   => $vaccination->fresh()->load('vaccine'),
                ]);
            }

            return redirect()->back()->with('success', 'Vaccination record updated successfully.');
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
                return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }
}
