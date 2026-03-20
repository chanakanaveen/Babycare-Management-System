<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VaccineScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $babyName;
    public $vaccineName;
    public $scheduledDate;
    public $midwifeName;
    public $clinicLocation;

    public function __construct(string $babyName, string $vaccineName, string $scheduledDate, string $midwifeName, string $clinicLocation = 'Local Health Clinic')
    {
        $this->babyName = $babyName;
        $this->vaccineName = $vaccineName;
        $this->scheduledDate = $scheduledDate;
        $this->midwifeName = $midwifeName;
        $this->clinicLocation = $clinicLocation;
    }

    public function build()
    {
        return $this->subject("Vaccination Scheduled for {$this->babyName}")
                    ->view('emails.vaccine_scheduled');
    }
}
