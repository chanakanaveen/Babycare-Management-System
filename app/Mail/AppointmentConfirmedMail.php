<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $midwifeName;
    public $appointmentDate;
    public $appointmentTime;

    public function __construct(string $midwifeName, string $appointmentDate, string $appointmentTime)
    {
        $this->midwifeName = $midwifeName;
        $this->appointmentDate = $appointmentDate;
        $this->appointmentTime = $appointmentTime;
    }

    public function build()
    {
        return $this->subject("Your Appointment is Confirmed")
                    ->view('emails.appointment_confirmed');
    }
}
