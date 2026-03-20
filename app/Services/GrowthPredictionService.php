<?php

namespace App\Services;

use App\Models\Baby;
use App\Models\WeightRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GrowthPredictionService
{
    protected $apiKey;
    protected $model;
    protected $endpoint;
    protected $fallback;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');
        $this->fallback = new AiGrowthPredictionService();
    }

    /**
     * Generate an enhanced growth prediction for a baby from a specific record.
     */
    public function generatePrediction(Baby $baby, WeightRecord $record): array
    {
        if (empty($this->apiKey)) {
            Log::info('GrowthPredictionService: No API key, using fallback.');
            return $this->fallback->predict($this->buildBasicData($baby, $record));
        }

        try {
            $prompt = $this->buildEnhancedPrompt($baby, $record);

            $url = "{$this->endpoint}/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(45)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 2048,
                ],
            ]);

            if ($response->successful()) {
                $body = $response->json();
                $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    $parsed = $this->parseResponse($text);
                    if ($parsed) {
                        $parsed['source'] = 'gemini_ai_enhanced';
                        $parsed['generated_at'] = now()->toIso8601String();
                        return $parsed;
                    }
                }
            }

            Log::warning('GrowthPredictionService: Unexpected response from Gemini.', [
                'status' => $response->status(),
            ]);

            return $this->fallback->predict($this->buildBasicData($baby, $record));

        } catch (\Exception $e) {
            Log::error('GrowthPredictionService: API call failed.', [
                'error' => $e->getMessage(),
            ]);
            return $this->fallback->predict($this->buildBasicData($baby, $record));
        }
    }

    /**
     * Build an enhanced prompt with full baby and growth context.
     */
    protected function buildEnhancedPrompt(Baby $baby, WeightRecord $record): string
    {
        $gender = $baby->gender ?? 'unknown';
        $dob = $baby->date_of_birth ?? 'unknown';
        $birthWeight = $baby->birth_weight ?? 'N/A';

        // Get all historical records
        $history = $baby->growthRecords()
            ->orderBy('record_date', 'asc')
            ->get(['weight', 'height', 'age_months', 'record_date', 'milestones']);

        $historyText = '';
        if ($history->count() > 0) {
            $historyText = "Growth History (chronological):\n";
            foreach ($history as $rec) {
                $historyText .= "- Date: {$rec->record_date}, Age: {$rec->age_months}m, Weight: {$rec->weight}kg, Height: {$rec->height}cm";
                if ($rec->milestones) $historyText .= ", Milestones: {$rec->milestones}";
                $historyText .= "\n";
            }
        }

        $currentWeight = $record->weight ?? 'N/A';
        $currentHeight = $record->height ?? 'N/A';
        $currentAge = $record->age_months ?? 'N/A';
        $milestones = $record->milestones ?? 'None reported';

        return <<<PROMPT
You are a pediatric health AI assistant specializing in child growth analysis. Analyze this child's growth data against WHO Child Growth Standards and provide a comprehensive health assessment.

Baby Profile:
- Gender: {$gender}
- Date of Birth: {$dob}
- Birth Weight: {$birthWeight} kg

Current Measurements:
- Age: {$currentAge} months
- Weight: {$currentWeight} kg
- Height: {$currentHeight} cm
- Milestones: {$milestones}

{$historyText}

Please respond ONLY with a valid JSON object (no markdown, no code fences, no explanation outside the JSON) with the following structure:
{
    "growth_status": "One of: 'Normal', 'Above Average', 'Below Average', 'At Risk', 'Critical'",
    "overall_summary": "A 2-3 sentence narrative summary of the child's growth and health status, written for parents to understand",
    "weight_analysis": {
        "status": "One of: 'Normal', 'Underweight', 'Overweight'",
        "percentile": "Estimated WHO percentile (e.g., '50th percentile')",
        "detail": "Brief explanation"
    },
    "height_analysis": {
        "status": "One of: 'Normal', 'Short', 'Tall'",
        "percentile": "Estimated WHO percentile",
        "detail": "Brief explanation"
    },
    "bmi_category": "BMI category for age (e.g., 'Normal', 'Underweight', 'Overweight')",
    "growth_trend": "Description of the growth trajectory based on history",
    "predictions": {
        "next_month_weight": "Predicted weight in kg for next month",
        "next_month_height": "Predicted height in cm for next month",
        "growth_velocity": "Description of growth speed"
    },
    "recommendations": ["Array of 3-5 actionable recommendations"],
    "red_flags": ["Array of any concerning signs, empty array if none"],
    "follow_up_urgency": "One of: 'Routine', 'Soon', 'Urgent'"
}
PROMPT;
    }

    /**
     * Parse the AI response text into a structured array.
     */
    protected function parseResponse(string $text): ?array
    {
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [
                'growth_status'    => $decoded['growth_status'] ?? 'Normal',
                'overall_summary'  => $decoded['overall_summary'] ?? 'Unable to generate summary.',
                'weight_analysis'  => $decoded['weight_analysis'] ?? ['status' => 'N/A', 'percentile' => 'N/A', 'detail' => ''],
                'height_analysis'  => $decoded['height_analysis'] ?? ['status' => 'N/A', 'percentile' => 'N/A', 'detail' => ''],
                'bmi_category'     => $decoded['bmi_category'] ?? 'N/A',
                'growth_trend'     => $decoded['growth_trend'] ?? 'Unable to determine',
                'predictions'      => $decoded['predictions'] ?? [],
                'recommendations'  => $decoded['recommendations'] ?? [],
                'red_flags'        => $decoded['red_flags'] ?? [],
                'follow_up_urgency'=> $decoded['follow_up_urgency'] ?? 'Routine',
            ];
        }

        Log::warning('GrowthPredictionService: Failed to parse response.', [
            'raw' => substr($text, 0, 500),
        ]);

        return null;
    }

    /**
     * Build basic data for fallback service.
     */
    protected function buildBasicData(Baby $baby, WeightRecord $record): array
    {
        return [
            'weight' => $record->weight,
            'height' => $record->height,
            'age_months' => $record->age_months,
            'milestones' => $record->milestones,
            'gender' => $baby->gender,
        ];
    }
}
