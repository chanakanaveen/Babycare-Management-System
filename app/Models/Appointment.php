<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'parent_id',
        'midwife_id',
        'baby_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'reason',
        'status',
        'midwife_notes',
        'rejection_reason',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function parentUser()
    {
        return $this->belongsTo(ParentUser::class, 'parent_id');
    }

    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }

    public function baby()
    {
        return $this->belongsTo(Baby::class, 'baby_id', 'baby_id');
    }

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class, 'appointment_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                     ->whereIn('status', ['pending', 'confirmed']);
    }
}
