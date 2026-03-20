<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidwifeAvailability extends Model
{
    use HasFactory;

    protected $table = 'midwife_availability';

    protected $fillable = [
        'midwife_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }

    /**
     * Get day name from day_of_week number.
     */
    public function getDayNameAttribute(): string
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$this->day_of_week] ?? 'Unknown';
    }
}
