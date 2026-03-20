<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parentName;
    public $appointmentDate;
    public $appointmentTime;
    public $reason;
    public $babyName;

    public function __construct(string $parentName, string $appointmentDate, string $appointmentTime, string $reason, string $babyName = '')
    {
        $this->parentName = $parentName;
        $this->appointmentDate = $appointmentDate;
        $this->appointmentTime = $appointmentTime;
        $this->reason = $reason;
        $this->babyName = $babyName;
    }

    public function build()
    {
        return $this->subject("New Appointment Request from {$this->parentName}")
                    ->view('emails.appointment_request');
    }
}
