<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Mail\ContactMessageNotification;
use Illuminate\Support\Facades\Mail;
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

            // Send email to all admins
            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new ContactMessageNotification($contactData));
                }
            }

            return redirect()
                ->route('contact.create')
                ->with('success', __('Thank you for your message! We will get back to you soon.'));

        } catch (\Exception $e) {
            \Log::error('Failed to send contact message email notification', [
                'contact_data' => $contactData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('contact.create')
                ->with('error', __('There was an error sending your message. Please try again later.'));
        }
    }
}