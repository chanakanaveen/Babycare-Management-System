<?php

namespace App\Http\Middleware;

use App\Models\ChatRoom;
use Closure;
use Illuminate\Http\Request;

class EnsureAppointmentConfirmed
{
    public function handle(Request $request, Closure $next)
    {
        $chatRoomId = $request->route('chatRoomId');

        if ($chatRoomId) {
            $chatRoom = ChatRoom::with('appointment')->find($chatRoomId);

            if (!$chatRoom || !$chatRoom->appointment || $chatRoom->appointment->status !== 'confirmed') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'msg' => 'Chat is only available for confirmed appointments.',
                    ], 403);
                }
                return redirect()->back()->with('fail', 'Chat is only available for confirmed appointments.');
            }
        }

        return $next($request);
    }
}
