<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';

    protected $fillable = [
        'appointment_id',
        'parent_id',
        'midwife_id',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function parentUser()
    {
        return $this->belongsTo(ParentUser::class, 'parent_id');
    }

    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'chat_room_id')->latest('created_at');
    }

    /**
     * Get unread count for a specific user.
     */
    public function unreadCountFor(string $recipientType, int $recipientId): int
    {
        return $this->messages()
            ->where('is_read', false)
            ->where(function ($q) use ($recipientType, $recipientId) {
                $q->where('sender_type', '!=', $recipientType)
                  ->orWhere('sender_id', '!=', $recipientId);
            })
            ->count();
    }
}
