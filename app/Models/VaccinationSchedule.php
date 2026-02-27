<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $table = 'vaccination_schedules';
    protected $primaryKey = 'vaccine_id';

    protected $fillable = [
        'vaccine_name',
        'description',
        'recommended_age_months',
        'doses_required',
        'dose_schedule',
        'is_mandatory',
        'status',
    ];

    protected $casts = [
        'dose_schedule' => 'array',
        'is_mandatory' => 'boolean',
    ];

    public $timestamps = false;

    /**
     * Get all individual baby vaccination records for this vaccine.
     */
    public function babyVaccinations()
    {
        return $this->hasMany(BabyVaccination::class, 'vaccine_id', 'vaccine_id');
    }
}
