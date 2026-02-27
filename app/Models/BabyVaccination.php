<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BabyVaccination extends Model
{
    use HasFactory;

    protected $table = 'baby_vaccinations';
    protected $primaryKey = 'record_id';

    protected $fillable = [
        'baby_id',
        'vaccine_id',
        'dose_number',
        'administered_date',
        'midwife_id',
        'next_dose_date',
        'batch_number',
        'notes',
        'vaccination_status',
        'scheduled_date',
        'reminder_sent',
    ];

    protected $casts = [
        'reminder_sent' => 'boolean',
        'administered_date' => 'date',
        'scheduled_date' => 'date',
        'next_dose_date' => 'date',
    ];

    public $timestamps = true;

    /**
     * Get the baby this vaccination belongs to.
     */
    public function baby()
    {
        return $this->belongsTo(Baby::class, 'baby_id', 'baby_id');
    }

    /**
     * Get the vaccination schedule (vaccine) for this record.
     */
    public function vaccine()
    {
        return $this->belongsTo(VaccinationSchedule::class, 'vaccine_id', 'vaccine_id');
    }

    /**
     * Get the midwife who administered this vaccination.
     */
    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }

    /**
     * Scope: upcoming vaccinations within the next N days.
     */
    public function scopeUpcomingInDays($query, $days = 3)
    {
        return $query->where('vaccination_status', 'scheduled')
                     ->where('reminder_sent', false)
                     ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }
}
