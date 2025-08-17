<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display all messages for admin management
     */
    public function index(Request $request)
    {
        $query = Message::with(['sender', 'receiver', 'product']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%")
                  ->orWhereHas('sender', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('receiver', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by read status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by user
        if ($request->filled('user')) {
            $userId = $request->get('user');
            $query->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            });
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get users for filter dropdown
        $users = User::orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => Message::count(),
            'unread' => Message::whereNull('read_at')->count(),
            'today' => Message::whereDate('created_at', today())->count(),
            'this_week' => Message::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.messages.index', compact('messages', 'users', 'stats'));
    }

    /**
     * Show a specific message
     */
    public function show(Message $message)
    {
        $message->load(['sender', 'receiver', 'product']);
        
        // Get conversation context (messages between the same users)
        $conversation = Message::betweenUsers($message->sender_id, $message->receiver_id)
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.messages.show', compact('message', 'conversation'));
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', __('Message deleted successfully.'));
    }

    /**
     * Mark message as read/unread
     */
    public function toggleRead(Message $message)
    {
        if ($message->read_at) {
            $message->update(['read_at' => null]);
            $status = __('marked as unread');
        } else {
            $message->update(['read_at' => now()]);
            $status = __('marked as read');
        }

        return back()->with('success', __('Message :status successfully.', ['status' => $status]));
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_read,mark_unread',
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        $messageIds = $request->get('message_ids');
        $action = $request->get('action');

        switch ($action) {
            case 'delete':
                Message::whereIn('id', $messageIds)->delete();
                $message = __(':count messages deleted successfully.', ['count' => count($messageIds)]);
                break;
            
            case 'mark_read':
                Message::whereIn('id', $messageIds)->update(['read_at' => now()]);
                $message = __(':count messages marked as read.', ['count' => count($messageIds)]);
                break;
            
            case 'mark_unread':
                Message::whereIn('id', $messageIds)->update(['read_at' => null]);
                $message = __(':count messages marked as unread.', ['count' => count($messageIds)]);
                break;
        }

        return back()->with('success', $message);
    }
}