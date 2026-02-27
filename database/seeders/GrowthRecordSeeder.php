<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrowthRecordSeeder extends Seeder
{
    /**
     * Seed realistic monthly growth records with AI predictions for testing.
     */
    public function run()
    {
        // WHO-based growth data per age in months [weight_kg, height_cm, head_cm]
        // Male baseline (slightly higher than female)
        $maleGrowth = [
            0  => [3.5,  50.0, 34.5],
            1  => [4.5,  54.5, 37.0],
            2  => [5.6,  58.0, 39.0],
            3  => [6.4,  61.0, 40.5],
            4  => [7.0,  63.5, 41.5],
            5  => [7.5,  65.5, 42.5],
            6  => [7.9,  67.5, 43.5],
            7  => [8.3,  69.0, 44.0],
            8  => [8.6,  70.5, 44.5],
            9  => [8.9,  72.0, 45.0],
            10 => [9.2,  73.5, 45.5],
            11 => [9.4,  74.5, 46.0],
            12 => [9.6,  76.0, 46.5],
            15 => [10.3, 79.0, 47.5],
            18 => [10.9, 82.0, 48.0],
            24 => [12.1, 87.5, 49.0],
        ];

        $femaleGrowth = [
            0  => [3.3,  49.0, 33.5],
            1  => [4.2,  53.5, 36.0],
            2  => [5.1,  57.0, 38.0],
            3  => [5.8,  59.5, 39.5],
            4  => [6.4,  62.0, 40.5],
            5  => [6.9,  64.0, 41.5],
            6  => [7.3,  66.0, 42.5],
            7  => [7.6,  67.5, 43.0],
            8  => [7.9,  69.0, 43.5],
            9  => [8.2,  70.5, 44.0],
            10 => [8.5,  72.0, 44.5],
            11 => [8.7,  73.0, 45.0],
            12 => [8.9,  74.5, 45.5],
            15 => [9.6,  77.5, 46.5],
            18 => [10.2, 80.5, 47.0],
            24 => [11.5, 86.0, 48.0],
        ];

        $milestones = [
            0  => 'Rooting reflex, strong grip, responds to sound',
            1  => 'Smiling, following objects with eyes, lifting head briefly',
            2  => 'Social smile, cooing, holding head up during tummy time',
            3  => 'Laughing, reaching for objects, holding head steady',
            4  => 'Rolling over, babbling, sitting with support',
            5  => 'Sitting without support briefly, transferring objects between hands',
            6  => 'Sitting independently, beginning to stand with support, babbling mama/dada',
            7  => 'Crawling, waving bye-bye, pulling to stand',
            8  => 'Cruising along furniture, pincer grasp developing',
            9  => 'Standing independently briefly, first words emerging',
            10 => 'Walking with assistance, imitating actions, pointing at objects',
            11 => 'First steps, vocabulary of 1-3 words, understanding simple commands',
            12 => 'Walking independently, vocabulary of 3-5 words, using cup',
            15 => 'Running, vocabulary expanding to 10+ words, scribbling',
            18 => 'Speaking in 2-word phrases, climbing stairs with support, pretend play',
            24 => 'Running well, speaking 50+ words and 2-3 word sentences, toilet training beginning',
        ];

        // AI prediction templates based on status
        $normalPrediction = function($baby, $ageMonths, $weight, $height, $nextWeight) {
            $heightM = $height / 100;
            $bmi = round($weight / ($heightM * $heightM), 1);
            return [
                'bmi_status'          => 'Normal',
                'growth_status'       => 'On track',
                'recommendations'     => [
                    'Continue breastfeeding or appropriate formula feeding',
                    'Introduce age-appropriate solid foods if over 6 months',
                    'Ensure adequate tummy time and physical activity',
                    'Maintain regular vaccination schedule',
                    'Schedule next growth check in 1 month',
                ],
                'next_checkup_weight' => $nextWeight,
                'concerns'            => [],
                'milestone_expectations' => 'Baby is progressing well. At ' . $ageMonths . ' months, expect continued development in motor skills and social interaction. ' . ($ageMonths < 12 ? 'Focus on sensory stimulation and responsive caregiving.' : 'Encourage language development through reading and talking.'),
                'bmi'                 => $bmi,
                'source'              => 'gemini_ai',
            ];
        };

        $underweightPrediction = function($baby, $ageMonths, $weight, $height, $nextWeight) {
            return [
                'bmi_status'          => 'Underweight',
                'growth_status'       => 'Below average',
                'recommendations'     => [
                    'Increase feeding frequency — offer breast/bottle every 2-3 hours',
                    'Consult a pediatric nutritionist for a tailored feeding plan',
                    'Consider fortified foods if over 6 months',
                    'Monitor for signs of illness or feeding difficulties',
                    'Schedule follow-up in 2 weeks to track weight gain',
                ],
                'next_checkup_weight' => $nextWeight,
                'concerns'            => [
                    'Weight is below the expected range for age — early intervention recommended',
                    'Ensure adequate caloric intake and rule out underlying conditions',
                ],
                'milestone_expectations' => 'Nutritional support is key at this stage. With improved nutrition, the baby should catch up on milestones. Monitor energy levels and alertness as indicators of improvement.',
                'source'              => 'gemini_ai',
            ];
        };

        // Babies to seed growth records for
        // Format: [baby_id, gender, midwife_id, parent_id, age_months_start, variation]
        // variation: 0=normal, 1=slightly underweight
        $babyConfigs = [
            ['id' => 1,  'gender' => 'male',   'midwife' => 1, 'parent' => 1,  'ages' => [0,1,2,3,4,5,6,7,8,9,10,11,12], 'var' => 0],
            ['id' => 2,  'gender' => 'female', 'midwife' => 2, 'parent' => 2,  'ages' => [0,1,2,3,4,5,6,7,8,9,10,11,12], 'var' => 0],
            ['id' => 3,  'gender' => 'female', 'midwife' => 2, 'parent' => 1,  'ages' => [0,2,4,6,8,10,12], 'var' => 1],
            ['id' => 4,  'gender' => 'male',   'midwife' => 1, 'parent' => 1,  'ages' => [0,3,6,9,12,15,18,24], 'var' => 0],
            ['id' => 5,  'gender' => 'female', 'midwife' => 2, 'parent' => 2,  'ages' => [0,2,4,6,9,12], 'var' => 0],
            ['id' => 6,  'gender' => 'male',   'midwife' => 3, 'parent' => 3,  'ages' => [0,1,2,3,4,5,6], 'var' => 0],
            ['id' => 7,  'gender' => 'female', 'midwife' => 2, 'parent' => 4,  'ages' => [0,3,6,9,12], 'var' => 0],
            ['id' => 8,  'gender' => 'male',   'midwife' => 1, 'parent' => 5,  'ages' => [0,2,4,6,8,10,12,15,18], 'var' => 0],
            ['id' => 9,  'gender' => 'female', 'midwife' => 2, 'parent' => 6,  'ages' => [0,3,6,9,12,15,18,24], 'var' => 1],
            ['id' => 10, 'gender' => 'male',   'midwife' => 3, 'parent' => 7,  'ages' => [0,1,2,3,4,5,6,9,12], 'var' => 0],
        ];

        $now = Carbon::now();

        foreach ($babyConfigs as $config) {
            $growthTable = $config['gender'] === 'male' ? $maleGrowth : $femaleGrowth;
            $ageList = $config['ages'];

            foreach ($ageList as $idx => $ageMonths) {
                if (!isset($growthTable[$ageMonths])) continue;

                [$baseWeight, $baseHeight, $baseHead] = $growthTable[$ageMonths];

                // Add small random variation ±5%
                $wVariation = $config['var'] === 1 ? -0.08 : (($idx % 3 - 1) * 0.02);
                $weight = round($baseWeight * (1 + $wVariation) + (($idx % 5) * 0.05), 2);
                $height = round($baseHeight + ($idx % 3) * 0.2, 1);
                $head   = round($baseHead + ($idx % 2) * 0.1, 1);

                // Record date: simulate monthly records going back from today
                $monthsBack = count($ageList) - 1 - $idx;
                $recordDate = $now->copy()->subMonths($monthsBack)->format('Y-m-d');

                // Next expected weight
                $nextAgeKey = array_keys($growthTable)[array_search($ageMonths, array_keys($growthTable)) + 1] ?? null;
                $nextWeight = $nextAgeKey ? round($growthTable[$nextAgeKey][0] * (1 + $wVariation) + 0.1, 2) : round($weight + 0.4, 2);

                // Build AI prediction
                $prediction = ($config['var'] === 1 && $ageMonths > 0)
                    ? $underweightPrediction($config, $ageMonths, $weight, $height, $nextWeight)
                    : $normalPrediction($config, $ageMonths, $weight, $height, $nextWeight);

                $milestone = $milestones[$ageMonths] ?? null;

                // Skip if record already exists for this baby at this age_months
                $exists = DB::table('weight_record')
                    ->where('baby_id', $config['id'])
                    ->where('age_months', $ageMonths)
                    ->exists();

                if ($exists) continue;

                DB::table('weight_record')->insert([
                    'baby_id'            => $config['id'],
                    'weight'             => $weight,
                    'height'             => $height,
                    'head_circumference' => $head,
                    'age_months'         => $ageMonths,
                    'milestones'         => $milestone,
                    'midwife_id'         => $config['midwife'],
                    'record_date'        => $recordDate,
                    'notes'              => 'Seeded sample record for testing.',
                    'ai_prediction'      => json_encode($prediction),
                    'created_at'         => $now->copy()->subMonths($monthsBack)->toDateTimeString(),
                    'updated_at'         => $now->copy()->subMonths($monthsBack)->toDateTimeString(),
                ]);
            }

            // Update BMI on baby record using latest weight/height
            $latest = DB::table('weight_record')
                ->where('baby_id', $config['id'])
                ->orderByDesc('record_date')
                ->first();

            if ($latest && $latest->height > 0) {
                $hm = $latest->height / 100;
                $bmi = round($latest->weight / ($hm * $hm), 2);
                DB::table('baby')->where('baby_id', $config['id'])->update(['bmi' => $bmi]);
            }
        }

        $this->command->info('Growth records seeded successfully for 10 babies.');
    }
}
