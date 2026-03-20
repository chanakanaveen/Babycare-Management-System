<?php

namespace App\Jobs;

use App\Mail\VaccineScheduledMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVaccineScheduledEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $parentEmail;
    public $babyName;
    public $vaccineName;
    public $scheduledDate;
    public $midwifeName;
    public $clinicLocation;

    public $tries = 3;

    public function __construct(
        string $parentEmail,
        string $babyName,
        string $vaccineName,
        string $scheduledDate,
        string $midwifeName,
        string $clinicLocation = 'Local Health Clinic'
    ) {
        $this->parentEmail = $parentEmail;
        $this->babyName = $babyName;
        $this->vaccineName = $vaccineName;
        $this->scheduledDate = $scheduledDate;
        $this->midwifeName = $midwifeName;
        $this->clinicLocation = $clinicLocation;
        $this->onQueue('notifications');
    }

    public function handle()
    {
        try {
            Mail::to($this->parentEmail)->send(new VaccineScheduledMail(
                $this->babyName,
                $this->vaccineName,
                $this->scheduledDate,
                $this->midwifeName,
                $this->clinicLocation
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send vaccine scheduled email', [
                'email' => $this->parentEmail,
                'baby' => $this->babyName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
