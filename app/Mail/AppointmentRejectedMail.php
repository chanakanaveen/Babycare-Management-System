<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $midwifeName;
    public $appointmentDate;
    public $appointmentTime;
    public $rejectionReason;

    public function __construct(string $midwifeName, string $appointmentDate, string $appointmentTime, string $rejectionReason)
    {
        $this->midwifeName = $midwifeName;
        $this->appointmentDate = $appointmentDate;
        $this->appointmentTime = $appointmentTime;
        $this->rejectionReason = $rejectionReason;
    }

    public function build()
    {
        return $this->subject("Your Appointment Request Was Declined")
                    ->view('emails.appointment_rejected');
    }
}
