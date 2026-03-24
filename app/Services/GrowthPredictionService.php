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
    "bmi_status": "BMI category for age (e.g., 'Normal', 'Underweight', 'Overweight')",
    "growth_status": "One of: 'Normal', 'Above Average', 'Below Average', 'At Risk', 'Critical'",
    "next_checkup_weight": "Predicted weight in kg for next month (number only, e.g. 8.5)",
    "recommendations": ["Array of 3-5 actionable recommendations"],
    "concerns": ["Array of any concerning signs, empty array if none"],
    "milestone_expectations": "A brief summary of what milestones to expect next based on current age",
    "overall_summary": "A 2-3 sentence narrative summary of the child's growth and health status",
    "growth_trend": "Description of the growth trajectory based on history"
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
                'bmi_status'           => $decoded['bmi_status'] ?? 'N/A',
                'growth_status'        => $decoded['growth_status'] ?? 'Normal',
                'next_checkup_weight'  => $decoded['next_checkup_weight'] ?? null,
                'recommendations'      => $decoded['recommendations'] ?? [],
                'concerns'             => $decoded['concerns'] ?? [],
                'milestone_expectations'=> $decoded['milestone_expectations'] ?? null,
                'overall_summary'      => $decoded['overall_summary'] ?? 'Unable to generate summary.',
                'growth_trend'         => $decoded['growth_trend'] ?? 'Unable to determine',
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
