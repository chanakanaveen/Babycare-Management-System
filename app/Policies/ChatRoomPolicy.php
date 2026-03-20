<?php

namespace App\Policies;

use App\Models\ChatRoom;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ChatRoomPolicy
{
    /**
     * Check if the user can access this chat room.
     */
    public function access(Authenticatable $user, ChatRoom $chatRoom): bool
    {
        // Check if user is the parent or midwife of this chat room
        if ($user instanceof \App\Models\ParentUser) {
            return $chatRoom->parent_id === $user->id;
        }
        if ($user instanceof \App\Models\Midwife) {
            return $chatRoom->midwife_id === $user->id;
        }
        return false;
    }
}
