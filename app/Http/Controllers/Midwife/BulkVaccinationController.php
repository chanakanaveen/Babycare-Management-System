<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\BabyVaccination;
use App\Models\VaccinationSchedule;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BulkVaccinationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function create()
    {
        $vaccines = VaccinationSchedule::where('status', 1)->get();
        
        $data = [
            'pageTitle' => 'Bulk Vaccination Scheduler',
            'vaccines'  => $vaccines,
        ];

        return view('back.pages.midwife.vaccination-schedule-management', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vaccine_id'     => 'required|exists:vaccination_schedules,vaccine_id',
            'dose_number'    => 'required|integer|min:1',
            'min_age_months' => 'required|integer|min:0',
            'max_age_months' => 'required|integer|gte:min_age_months',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'notes'          => 'nullable|string|max:500',
        ]);

        $midwifeId = Auth::guard('midwife')->id();

        // Find babies assigned to this midwife within the age range
        $babies = Baby::where('midwife_id', $midwifeId)->get();
        
        $matchedBabies = collect();
        foreach ($babies as $baby) {
            $ageMonths = Carbon::parse($baby->date_of_birth)->diffInMonths(now());
            if ($ageMonths >= $request->min_age_months && $ageMonths <= $request->max_age_months) {
                // Optionally: Check if they already have this dose scheduled to avoid duplicates
                $exists = BabyVaccination::where('baby_id', $baby->baby_id)
                    ->where('vaccine_id', $request->vaccine_id)
                    ->where('dose_number', $request->dose_number)
                    ->exists();

                if (!$exists) {
                    $matchedBabies->push($baby);
                }
            }
        }

        if ($matchedBabies->isEmpty()) {
            return redirect()->back()->with('warning', 'No new eligible babies were found in that specific age range (they might already be scheduled).');
        }

        $count = 0;
        foreach ($matchedBabies as $baby) {
            // Schedule the vaccination
            $vaccination = BabyVaccination::create([
                'baby_id'             => $baby->baby_id,
                'vaccine_id'          => $request->vaccine_id,
                'dose_number'         => $request->dose_number,
                'midwife_id'          => $midwifeId,
                'scheduled_date'      => $request->scheduled_date,
                'vaccination_status'  => 'scheduled',
                'notes'               => $request->notes,
                'reminder_sent'       => false,
            ]);

            // Notify Parent
            $this->notificationService->notifyParentVaccineScheduled($vaccination->load('vaccine'));
            $count++;
        }

        return redirect()->back()->with('success', "Successfully scheduled vaccination for {$count} matching babies and dispatched parent notifications.");
    }
}
