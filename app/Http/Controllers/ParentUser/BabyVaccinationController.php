<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\BabyVaccination;
use App\Models\VaccinationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BabyVaccinationController extends Controller
{
    /**
     * List all vaccinations for a specific baby belonging to the authenticated parent.
     */
    public function index(Request $request, $babyId)
    {
        try {
            $parentId = Auth::guard('parent')->id();

            // Verify the baby belongs to this parent
            $baby = Baby::where('baby_id', $babyId)
                        ->where('parent_id', $parentId)
                        ->first();

            if (!$baby) {
                return redirect()->back()->with('fail', 'Baby not found or does not belong to you.');
            }

            $existingVaccinations = BabyVaccination::with('vaccine', 'midwife')
                ->where('baby_id', $babyId)
                ->get()
                ->groupBy('vaccine_id');

            $vaccines = VaccinationSchedule::where('status', 1)->get();
            $babyAgeMonths = (int) \Carbon\Carbon::parse($baby->date_of_birth)->diffInMonths(now());
            $vaccinationList = collect();

            foreach ($vaccines as $vaccine) {
                $doses = $vaccine->doses_required;
                $doseSchedule = $vaccine->dose_schedule ?? [];
                if (is_string($doseSchedule)) {
                    $doseSchedule = json_decode($doseSchedule, true);
                }

                $existingForVaccine = $existingVaccinations->get($vaccine->vaccine_id) ?? collect();

                for ($dose = 1; $dose <= $doses; $dose++) {
                    $targetAge = $vaccine->recommended_age_months;
                    if (is_array($doseSchedule)) {
                        foreach ($doseSchedule as $sch) {
                            if (isset($sch['dose']) && $sch['dose'] == $dose && isset($sch['age_months'])) {
                                $targetAge = $sch['age_months'];
                                break;
                            }
                        }
                    }

                    $existingRecord = $existingForVaccine->where('dose_number', $dose)->first();
                    $calcStatus = 'unknown';

                    if ($existingRecord) {
                        if ($existingRecord->vaccination_status === 'administered') {
                            $calcStatus = 'administered';
                        } elseif ($existingRecord->vaccination_status === 'scheduled') {
                            $calcStatus = 'scheduled';
                        } else {
                            $calcStatus = $existingRecord->vaccination_status;
                        }
                    } else {
                        if ($babyAgeMonths > $targetAge + 1) {
                            $calcStatus = 'overdue';
                        } elseif ($babyAgeMonths >= $targetAge - 1 && $babyAgeMonths <= $targetAge + 1) {
                            $calcStatus = 'due_now';
                        } elseif ($targetAge - $babyAgeMonths > 1 && $targetAge - $babyAgeMonths <= 3) {
                            $calcStatus = 'upcoming';
                        } else {
                            $calcStatus = 'not_yet';
                        }
                    }

                    $vaccinationList->push((object)[
                        'vaccine'           => $vaccine,
                        'dose_number'       => $dose,
                        'target_age_months' => $targetAge,
                        'calculated_status' => $calcStatus,
                        'existing_record'   => $existingRecord,
                    ]);
                }
            }

            $vaccinationList = $vaccinationList->sortBy('target_age_months')->values();

            $data = [
                'pageTitle'     => 'Vaccination Schedule - ' . $baby->full_name,
                'baby'          => $baby,
                'babyAgeMonths' => $babyAgeMonths,
                'vaccinations'  => $vaccinationList,
            ];

            return view('back.pages.parent.baby-vaccinations', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }
}
