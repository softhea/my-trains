<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Message;
use App\Models\Role;
use App\Mail\ContactMessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function create(): View
    {
        return view('contact.create');
    }

    /**
     * Store a new contact message
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                if (!NoCaptcha::verifyResponse($value)) {
                    $fail(__('The reCAPTCHA verification failed. Please try again.'));
                }
            }],
        ]);

        $contactData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'submitted_at' => now(),
        ];

        try {
            return DB::transaction(function () use ($contactData, $request) {
                // Get all admins and superadmins
                $admins = User::whereHas('role', function ($query) {
                    $query->whereIn('name', ['admin', 'superadmin']);
                })->get();

                if ($admins->isEmpty()) {
                    // Fallback: get first user with admin-like name if no roles found
                    $admins = User::where('email', 'like', '%admin%')
                        ->orWhere('name', 'like', '%admin%')
                        ->get();
                }

                if ($admins->isEmpty()) {
                    throw new \Exception('No administrators found to receive contact messages.');
                }

                // Find or create sender user
                $sender = $this->findOrCreateContactUser($request->email, $request->name);

                // Create message for each admin in the message system
                foreach ($admins as $admin) {
                    Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $admin->id,
                        'product_id' => null, // Contact messages are not about specific products
                        'subject' => '[Contact Form] ' . $request->subject,
                        'message' => $request->message,
                    ]);
                }

                // Send email notifications to all admins
                foreach ($admins as $admin) {
                    if ($admin->email) {
                        Mail::to($admin->email)->send(new ContactMessageNotification($contactData));
                    }
                }

                return redirect()
                    ->route('contact.create')
                    ->with('success', __('Thank you for your message! We will get back to you soon.'));
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send contact message', [
                'contact_data' => $contactData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('contact.create')
                ->with('error', __('There was an error sending your message. Please try again later.'));
        }
    }

    /**
     * Find existing user by email or create a guest user for contact form
     */
    private function findOrCreateContactUser(string $email, string $name): User
    {
        // First, try to find existing user by email
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            return $existingUser;
        }

        // If user doesn't exist, create a guest user
        // Get the default user role
        $guestRole = Role::where('name', 'user')->first();
        if (!$guestRole) {
            // Fallback to any non-admin role
            $guestRole = Role::whereNotIn('name', ['admin', 'superadmin'])->first();
        }

        // Create guest user with a temporary password and mark as unverified
        $guestUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(uniqid()), // Random password since they won't use it to login
            'role_id' => $guestRole?->id,
            'email_verified_at' => null, // Mark as unverified to distinguish from regular users
            'is_protected' => false,
        ]);

        return $guestUser;
    }
}