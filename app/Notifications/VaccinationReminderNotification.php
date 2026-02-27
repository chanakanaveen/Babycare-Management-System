<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\BabyVaccination;

class VaccinationReminderNotification extends Notification
{
    use Queueable;

    protected $vaccination;
    protected $babyName;
    protected $vaccineName;

    /**
     * Create a new notification instance.
     */
    public function __construct(BabyVaccination $vaccination, string $babyName, string $vaccineName)
    {
        $this->vaccination = $vaccination;
        $this->babyName = $babyName;
        $this->vaccineName = $vaccineName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $scheduledDate = $this->vaccination->scheduled_date
            ? $this->vaccination->scheduled_date->format('Y-m-d')
            : 'N/A';

        $doseNumber = $this->vaccination->dose_number ?? 'N/A';

        return (new MailMessage)
            ->subject('Vaccination Reminder - ' . $this->babyName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a friendly reminder that a vaccination is due soon.')
            ->line('**Baby:** ' . $this->babyName)
            ->line('**Vaccine:** ' . $this->vaccineName)
            ->line('**Dose Number:** ' . $doseNumber)
            ->line('**Scheduled Date:** ' . $scheduledDate)
            ->line('Please ensure the vaccination is administered on time.')
            ->line('If you have any questions, please contact your assigned midwife or the MOH office.')
            ->salutation('Best regards, Babycare Management System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type'           => 'vaccination_reminder',
            'baby_name'      => $this->babyName,
            'vaccine_name'   => $this->vaccineName,
            'dose_number'    => $this->vaccination->dose_number,
            'scheduled_date' => $this->vaccination->scheduled_date?->format('Y-m-d'),
            'record_id'      => $this->vaccination->record_id,
        ];
    }
}
