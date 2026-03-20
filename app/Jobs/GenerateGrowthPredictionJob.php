<?php

namespace App\Jobs;

use App\Models\Baby;
use App\Models\WeightRecord;
use App\Services\GrowthPredictionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateGrowthPredictionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recordId;
    protected $babyId;

    public $tries = 2;

    public function __construct(int $recordId, int $babyId)
    {
        $this->recordId = $recordId;
        $this->babyId = $babyId;
        $this->onQueue('predictions');
    }

    public function handle()
    {
        try {
            $record = WeightRecord::findOrFail($this->recordId);
            $baby = Baby::findOrFail($this->babyId);

            $service = new GrowthPredictionService();
            $prediction = $service->generatePrediction($baby, $record);

            $record->update([
                'ai_prediction' => $prediction,
            ]);

            Log::info('GrowthPrediction generated', [
                'record_id' => $this->recordId,
                'source' => $prediction['source'] ?? 'unknown',
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateGrowthPredictionJob failed', [
                'record_id' => $this->recordId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
