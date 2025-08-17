<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display the user's inbox
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get conversations grouped by sender/receiver
        $conversations = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver', 'product'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($message) use ($user) {
            // Group by the "other" user in the conversation
            return $message->sender_id === $user->id 
                ? $message->receiver_id 
                : $message->sender_id;
        })
        ->map(function ($messages) {
            return $messages->sortByDesc('created_at');
        });

        return view('messages.index', compact('conversations'));
    }

    /**
     * Show conversation with a specific user
     */
    public function conversation(User $user)
    {
        $currentUser = Auth::user();
        
        if ($user->id === $currentUser->id) {
            return redirect()->route('messages.index')
                ->with('error', __('You cannot message yourself.'));
        }

        $messages = Message::betweenUsers($currentUser->id, $user->id)
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('user', 'messages'));
    }

    /**
     * Show form to send message
     */
    public function create(Request $request)
    {
        $receiver = null;
        $product = null;
        $subject = '';
        $users = [];

        if ($request->has('user_id')) {
            $receiver = User::findOrFail($request->user_id);
        }

        if ($request->has('product_id')) {
            $product = Product::with('user')->findOrFail($request->product_id);
            $receiver = $product->user;
            $subject = __('Question about: :product', ['product' => $product->name]);
        }

        // If no specific receiver, load all users except current user
        if (!$receiver) {
            $users = User::where('id', '!=', Auth::id())
                ->orderBy('name')
                ->get();
        }

        if ($receiver && $receiver->id === Auth::id()) {
            return redirect()->route('messages.index')
                ->with('error', __('You cannot message yourself.'));
        }

        return view('messages.create', compact('receiver', 'product', 'subject', 'users'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($request->receiver_id == Auth::id()) {
            return back()->with('error', __('You cannot message yourself.'));
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'product_id' => $request->product_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('messages.conversation', $request->receiver_id)
            ->with('success', __('Message sent successfully!'));
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', __('You cannot message yourself.'));
        }

        // Get the original subject from the latest message in conversation
        $latestMessage = Message::betweenUsers(Auth::id(), $user->id)
            ->latest()
            ->first();

        $subject = $latestMessage ? $latestMessage->subject : __('Reply');
        if (!str_starts_with($subject, 'Re:')) {
            $subject = 'Re: ' . $subject;
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'product_id' => $latestMessage?->product_id,
            'subject' => $subject,
            'message' => $request->message,
        ]);

        return back()->with('success', __('Reply sent successfully!'));
    }

    /**
     * Get unread messages count (for AJAX)
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadMessagesCount()
        ]);
    }

    /**
     * Get latest unread messages (for navbar dropdown)
     */
    public function latestUnread()
    {
        $messages = Auth::user()->latestUnreadMessages(5);
        
        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->sender->name,
                    'subject' => $message->subject,
                    'message' => \Illuminate\Support\Str::limit($message->message, 50),
                    'product' => $message->product ? $message->product->name : null,
                    'created_at' => $message->created_at->diffForHumans(),
                    'url' => route('messages.conversation', $message->sender),
                ];
            })
        ]);
    }
}