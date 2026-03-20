<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Determine current user type and ID.
     */
    protected function getCurrentUser(): array
    {
        if (Auth::guard('parent')->check()) {
            return ['parent', Auth::guard('parent')->id(), Auth::guard('parent')->user()];
        }
        if (Auth::guard('midwife')->check()) {
            return ['midwife', Auth::guard('midwife')->id(), Auth::guard('midwife')->user()];
        }
        abort(403);
        return ['', 0, null];
    }

    /**
     * Chat rooms list.
     */
    public function index()
    {
        [$type, $id, $user] = $this->getCurrentUser();

        $chatRooms = ChatRoom::with(['parentUser', 'midwife', 'appointment', 'latestMessage'])
            ->where($type === 'parent' ? 'parent_id' : 'midwife_id', $id)
            ->orderBy('last_message_at', 'desc')
            ->get();

        $viewPath = $type === 'parent' ? 'back.pages.parent.chat.index' : 'back.pages.midwife.chat.index';

        return view($viewPath, [
            'pageTitle' => 'Chats',
            'chatRooms' => $chatRooms,
            'userType' => $type,
            'userId' => $id,
        ]);
    }

    /**
     * Show chat room.
     */
    public function show($chatRoomId)
    {
        [$type, $id, $user] = $this->getCurrentUser();

        $chatRoom = ChatRoom::with(['parentUser', 'midwife', 'appointment'])
            ->findOrFail($chatRoomId);

        // Authorization check
        if ($type === 'parent' && $chatRoom->parent_id !== $id) abort(403);
        if ($type === 'midwife' && $chatRoom->midwife_id !== $id) abort(403);

        // Mark messages as read for this user
        ChatMessage::where('chat_room_id', $chatRoomId)
            ->where('sender_type', '!=', $type)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = ChatMessage::where('chat_room_id', $chatRoomId)
            ->orderBy('created_at', 'asc')
            ->get();

        $viewPath = $type === 'parent' ? 'back.pages.parent.chat.show' : 'back.pages.midwife.chat.show';

        return view($viewPath, [
            'pageTitle' => 'Chat',
            'chatRoom' => $chatRoom,
            'messages' => $messages,
            'userType' => $type,
            'userId' => $id,
            'otherUser' => $type === 'parent' ? $chatRoom->midwife : $chatRoom->parentUser,
        ]);
    }

    /**
     * Get messages (AJAX / pagination).
     */
    public function getMessages(Request $request, $chatRoomId)
    {
        [$type, $id] = $this->getCurrentUser();

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        if ($type === 'parent' && $chatRoom->parent_id !== $id) abort(403);
        if ($type === 'midwife' && $chatRoom->midwife_id !== $id) abort(403);

        $before = $request->get('before');
        $query = ChatMessage::where('chat_room_id', $chatRoomId)
            ->orderBy('created_at', 'desc')
            ->limit(30);

        if ($before) {
            $query->where('id', '<', $before);
        }

        $messages = $query->get()->reverse()->values();

        return response()->json([
            'status' => 1,
            'data' => $messages,
        ]);
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, $chatRoomId)
    {
        [$type, $id] = $this->getCurrentUser();

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        if ($type === 'parent' && $chatRoom->parent_id !== $id) abort(403);
        if ($type === 'midwife' && $chatRoom->midwife_id !== $id) abort(403);

        $request->validate([
            'message' => 'required_without:attachment|string|max:2000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx',
        ]);

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentType = in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'document';
        }

        $message = ChatMessage::create([
            'chat_room_id' => $chatRoomId,
            'sender_type' => $type,
            'sender_id' => $id,
            'message' => $request->input('message', ''),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'created_at' => now(),
        ]);

        $chatRoom->update(['last_message_at' => now()]);

        // Broadcast
        try {
            event(new ChatMessageSent($message->toArray(), $chatRoomId));
        } catch (\Exception $e) {
            Log::info('Chat broadcast not sent', ['error' => $e->getMessage()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 1,
                'data' => $message,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Mark messages as read.
     */
    public function markRead(Request $request, $chatRoomId)
    {
        [$type, $id] = $this->getCurrentUser();

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        if ($type === 'parent' && $chatRoom->parent_id !== $id) abort(403);
        if ($type === 'midwife' && $chatRoom->midwife_id !== $id) abort(403);

        $count = ChatMessage::where('chat_room_id', $chatRoomId)
            ->where('sender_type', '!=', $type)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['status' => 1, 'count' => $count]);
    }
}
