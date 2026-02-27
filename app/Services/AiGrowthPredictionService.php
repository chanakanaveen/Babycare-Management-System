<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGrowthPredictionService
{
    /**
     * Predict growth trends and health risks using Google Gemini AI (free tier).
     *
     * @param array $growthData {
     *     weight: float,
     *     height: float,
     *     age_months: int,
     *     milestones: string|null,
     *     gender: string,
     *     head_circumference: float|null,
     *     historical_records: array
     * }
     * @return array {
     *     growth_trend: string,
     *     percentile_estimate: string,
     *     health_risks: array,
     *     recommendations: array,
     *     bmi_category: string,
     *     source: string
     * }
     */
    public function predict(array $growthData): array
    {
        $apiKey = env('GEMINI_API_KEY');

        // If no API key configured, return mock prediction
        if (empty($apiKey)) {
            Log::info('AiGrowthPredictionService: No GEMINI_API_KEY set, using mock prediction.');
            return $this->getMockPrediction($growthData);
        }

        try {
            $prompt = $this->buildPrompt($growthData);

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 1024,
                    ]
                ]
            );

            if ($response->successful()) {
                $body = $response->json();
                $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    $parsed = $this->parseAiResponse($text);
                    if ($parsed) {
                        $parsed['source'] = 'gemini_ai';
                        return $parsed;
                    }
                }
            }

            Log::warning('AiGrowthPredictionService: Gemini API returned unexpected response.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getMockPrediction($growthData);

        } catch (\Exception $e) {
            Log::error('AiGrowthPredictionService: API call failed.', [
                'error' => $e->getMessage(),
            ]);

            return $this->getMockPrediction($growthData);
        }
    }

    /**
     * Build the AI prompt with growth data and WHO standards context.
     */
    protected function buildPrompt(array $growthData): string
    {
        $gender = $growthData['gender'] ?? 'unknown';
        $weight = $growthData['weight'] ?? 'N/A';
        $height = $growthData['height'] ?? 'N/A';
        $ageMonths = $growthData['age_months'] ?? 'N/A';
        $milestones = $growthData['milestones'] ?? 'None reported';
        $headCirc = $growthData['head_circumference'] ?? 'N/A';

        $historyText = '';
        if (!empty($growthData['historical_records'])) {
            $historyText = "Historical growth records:\n";
            foreach ($growthData['historical_records'] as $record) {
                $historyText .= "- Date: {$record['date']}, Weight: {$record['weight']}kg, Height: {$record['height']}cm, Age: {$record['age_months']} months\n";
            }
        }

        return <<<PROMPT
You are a pediatric health AI assistant. Analyze the following child's growth data against WHO Child Growth Standards and provide a health assessment.

Child Details:
- Gender: {$gender}
- Current Age: {$ageMonths} months
- Current Weight: {$weight} kg
- Current Height: {$height} cm
- Head Circumference: {$headCirc} cm
- Reported Milestones: {$milestones}

{$historyText}

Please respond ONLY with a valid JSON object (no markdown, no explanation outside the JSON) with the following structure:
{
    "growth_trend": "A brief description of the growth trajectory (e.g., 'Normal growth', 'Below average growth', 'Above average growth', 'Declining growth pattern')",
    "percentile_estimate": "Estimated WHO percentile range (e.g., '25th-50th percentile', '75th-90th percentile')",
    "health_risks": ["Array of identified health risks or concerns, if any"],
    "recommendations": ["Array of actionable recommendations for parents and healthcare providers"],
    "bmi_category": "BMI category for the child's age (e.g., 'Normal weight', 'Underweight', 'Overweight', 'At risk')"
}
PROMPT;
    }

    /**
     * Parse AI text response into structured array.
     */
    protected function parseAiResponse(string $text): ?array
    {
        // Remove markdown code fences if present
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Ensure all expected keys exist with defaults
            return [
                'growth_trend'       => $decoded['growth_trend'] ?? 'Unable to determine',
                'percentile_estimate'=> $decoded['percentile_estimate'] ?? 'Unable to determine',
                'health_risks'       => $decoded['health_risks'] ?? [],
                'recommendations'    => $decoded['recommendations'] ?? [],
                'bmi_category'       => $decoded['bmi_category'] ?? 'Unable to determine',
            ];
        }

        Log::warning('AiGrowthPredictionService: Failed to parse AI response as JSON.', [
            'raw_text' => substr($text, 0, 500),
        ]);

        return null;
    }

    /**
     * Return mock/placeholder prediction data for development or when API is unavailable.
     */
    public function getMockPrediction(array $growthData = []): array
    {
        $weight = $growthData['weight'] ?? 0;
        $height = $growthData['height'] ?? 0;
        $ageMonths = $growthData['age_months'] ?? 0;

        // Basic BMI calculation for mock category
        $bmiCategory = 'Normal weight';
        if ($height > 0) {
            $heightM = $height / 100;
            $bmi = $weight / ($heightM * $heightM);
            if ($bmi < 14) $bmiCategory = 'Underweight';
            elseif ($bmi > 18) $bmiCategory = 'Overweight';
        }

        return [
            'growth_trend'        => 'Normal growth pattern observed based on available data.',
            'percentile_estimate' => '25th-75th percentile (estimated)',
            'health_risks'        => [
                'No immediate health risks identified based on provided data.',
                'Continue regular health check-ups for ongoing monitoring.',
            ],
            'recommendations'     => [
                'Maintain a balanced diet appropriate for the child\'s age.',
                'Ensure regular physical activity and outdoor play.',
                'Follow the recommended vaccination schedule.',
                'Schedule the next growth assessment in 1-2 months.',
            ],
            'bmi_category'        => $bmiCategory,
            'source'              => 'mock_prediction',
        ];
    }
}
