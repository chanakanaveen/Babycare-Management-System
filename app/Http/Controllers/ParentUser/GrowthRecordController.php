<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\WeightRecord;
use App\Services\AiGrowthPredictionService;
use App\Services\GrowthPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class GrowthRecordController extends Controller
{
    protected $aiService;
    protected $growthPredictionService;

    public function __construct(AiGrowthPredictionService $aiService, GrowthPredictionService $growthPredictionService)
    {
        $this->aiService = $aiService;
        $this->growthPredictionService = $growthPredictionService;
    }

    /**
     * List all growth records for a specific baby belonging to the authenticated parent.
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
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'msg'    => 'Baby not found or does not belong to you.',
                    ], 403);
                }
                return redirect()->back()->with('fail', 'Baby not found or does not belong to you.');
            }

            $records = WeightRecord::where('baby_id', $babyId)
                ->orderBy('record_date', 'desc')
                ->get();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Growth records retrieved successfully.',
                    'data'   => [
                        'baby'    => $baby,
                        'records' => $records,
                    ],
                ]);
            }

            $data = [
                'pageTitle' => 'Growth Records - ' . $baby->full_name,
                'baby'      => $baby,
                'records'   => $records,
            ];

            return view('back.pages.parent.growth-records', $data);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show a single growth record with AI prediction.
     */
    public function show(Request $request, $id)
    {
        try {
            $parentId = Auth::guard('parent')->id();

            $record = WeightRecord::findOrFail($id);

            // Verify the baby belongs to this parent
            $baby = Baby::where('baby_id', $record->baby_id)
                        ->where('parent_id', $parentId)
                        ->first();

            if (!$baby) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'msg'    => 'Record not found or unauthorized.',
                    ], 403);
                }
                return redirect()->back()->with('fail', 'Record not found or unauthorized.');
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'msg'    => 'Growth record retrieved successfully.',
                    'data'   => [
                        'record'     => $record,
                        'prediction' => $record->ai_prediction,
                    ],
                ]);
            }

            return view('back.pages.parent.growth-record-detail', [
                'pageTitle'  => 'Growth Record Detail',
                'record'     => $record,
                'baby'       => $baby,
                'prediction' => $record->ai_prediction,
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Submit a new growth record for a baby (Parent).
     * Calls the AI Growth Prediction Service and stores the result.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'baby_id'    => 'required|exists:baby,baby_id',
                'weight'     => 'required|numeric|min:0.1|max:200',
                'height'     => 'required|numeric|min:1|max:250',
                'age_months' => 'required|integer|min:0|max:216',
                'milestones' => 'nullable|string|max:1000',
                'head_circumference' => 'nullable|numeric|min:0|max:100',
                'notes'      => 'nullable|string|max:500',
            ]);

            $parentId = Auth::guard('parent')->id();

            // Verify the baby belongs to this parent
            $baby = Baby::where('baby_id', $request->baby_id)
                        ->where('parent_id', $parentId)
                        ->first();

            if (!$baby) {
                return response()->json([
                    'status' => 0,
                    'msg'    => 'Baby not found or does not belong to you.',
                ], 403);
            }

            // Fetch historical records for AI context
            $historicalRecords = WeightRecord::where('baby_id', $request->baby_id)
                ->orderBy('record_date', 'asc')
                ->get()
                ->map(function ($record) {
                    return [
                        'weight'     => $record->weight,
                        'height'     => $record->height,
                        'age_months' => $record->age_months,
                        'date'       => $record->record_date,
                    ];
                })
                ->toArray();

            // Call AI Growth Prediction Service
            $prediction = $this->aiService->predict([
                'weight'             => $request->weight,
                'height'             => $request->height,
                'age_months'         => $request->age_months,
                'milestones'         => $request->milestones,
                'gender'             => $baby->gender,
                'head_circumference' => $request->head_circumference,
                'historical_records' => $historicalRecords,
            ]);

            // Calculate BMI
            $heightInMeters = $request->height / 100;
            $bmi = $request->weight / ($heightInMeters * $heightInMeters);

            // Save the growth record
            $record = WeightRecord::create([
                'baby_id'            => $request->baby_id,
                'weight'             => $request->weight,
                'height'             => $request->height,
                'head_circumference' => $request->head_circumference,
                'age_months'         => $request->age_months,
                'milestones'         => $request->milestones,
                'midwife_id'         => $baby->midwife_id,
                'record_date'        => Carbon::now()->format('Y-m-d'),
                'notes'              => $request->notes,
                'ai_prediction'      => $prediction,
            ]);

            // Update baby BMI
            $baby->bmi = round($bmi, 2);
            $baby->save();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status'     => 1,
                    'msg'        => 'Growth record saved successfully with AI prediction.',
                    'data'       => $record,
                    'prediction' => $prediction,
                ]);
            }

            return redirect()->back()->with('success', 'Growth record saved successfully with AI prediction.');
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
     * Generate (or regenerate) an AI prediction for an existing growth record.
     */
    public function generatePrediction(Request $request, $recordId)
    {
        try {
            $parentId = Auth::guard('parent')->id();

            $record = WeightRecord::where('record_id', $recordId)->firstOrFail();

            $baby = Baby::where('baby_id', $record->baby_id)
                        ->where('parent_id', $parentId)
                        ->first();

            if (!$baby) {
                return response()->json(['status' => 0, 'msg' => 'Unauthorized.'], 403);
            }

            $historicalRecords = WeightRecord::where('baby_id', $record->baby_id)
                ->where('record_id', '!=', $record->record_id)
                ->orderBy('record_date', 'asc')
                ->get()
                ->map(fn($r) => [
                    'weight'     => $r->weight,
                    'height'     => $r->height,
                    'age_months' => $r->age_months,
                    'date'       => $r->record_date,
                ])->toArray();

            $prediction = $this->growthPredictionService->generatePrediction($baby, $record);

            $record->ai_prediction = $prediction;
            $record->save();

            return response()->json([
                'status'     => 1,
                'msg'        => 'AI prediction generated successfully.',
                'prediction' => $prediction,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }
}
