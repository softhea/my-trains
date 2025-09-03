<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Message;
use App\Mail\ContactMessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class TestContactEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:contact-emails {--admin-email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test contact form email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminEmail = $this->option('admin-email');

        if ($adminEmail) {
            $admins = collect([
                (object) ['email' => $adminEmail, 'name' => 'Test Admin']
            ]);
        } else {
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
        }

        if ($admins->isEmpty()) {
            $this->error("No admin users found. Please specify an admin email with --admin-email=admin@example.com");
            return;
        }

        $this->info("Found " . $admins->count() . " admin(s) to notify:");
        foreach ($admins as $admin) {
            $this->info("  - {$admin->name} ({$admin->email})");
        }

        // Sample contact data
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => 'Test Contact Message',
            'message' => 'This is a test message sent through the contact form to verify that email notifications are working properly.',
            'submitted_at' => now(),
        ];

        $this->info("\n--- Testing Contact Form Email Notification ---");
        $this->info("Contact Data:");
        $this->info("  Name: {$contactData['name']}");
        $this->info("  Email: {$contactData['email']}");
        $this->info("  Subject: {$contactData['subject']}");
        $this->info("  Message: " . substr($contactData['message'], 0, 50) . "...");

        $successCount = 0;
        $failCount = 0;

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ContactMessageNotification($contactData));
                $this->info("✅ Contact notification sent successfully to {$admin->email}!");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to send contact notification to {$admin->email}: " . $e->getMessage());
                $failCount++;
            }
        }

        $this->info("\n🔔 Contact email testing completed!");
        $this->info("📧 Results: {$successCount} sent successfully, {$failCount} failed");
        $this->info("📧 Check your mail logs or configured mail service for the emails.");

        // Test the actual contact form integration (create messages in system)
        $this->info("\n--- Testing Contact Form Integration ---");
        try {
            // Create a test contact submission through the controller logic
            $testContactData = [
                'name' => 'Test User',
                'email' => 'test.contact@example.com',
                'subject' => 'Test Integration Message',
                'message' => 'This is a test to verify contact messages are created in the message system.',
            ];

            DB::transaction(function () use ($testContactData, $admins) {
                // Simulate the contact controller logic
                // Find or create user
                $sender = User::where('email', $testContactData['email'])->first();
                if (!$sender) {
                    $guestRole = \App\Models\Role::where('name', 'user')->first();
                    $sender = User::create([
                        'name' => $testContactData['name'],
                        'email' => $testContactData['email'],
                        'password' => bcrypt(uniqid()),
                        'role_id' => $guestRole?->id,
                        'email_verified_at' => null,
                    ]);
                    $this->info("✅ Created guest user: {$sender->name} ({$sender->email})");
                }

                // Create messages for each admin
                $messageCount = 0;
                foreach ($admins as $admin) {
                    Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $admin->id,
                        'product_id' => null,
                        'subject' => '[Contact Form] ' . $testContactData['subject'],
                        'message' => $testContactData['message'],
                    ]);
                    $messageCount++;
                }

                $this->info("✅ Created {$messageCount} messages in the system");
                $this->info("📨 Admins will see these messages in their inbox");
            });

        } catch (\Exception $e) {
            $this->error("❌ Failed to test contact integration: " . $e->getMessage());
            return 1;
        }

        $this->info("\n✅ All tests completed successfully!");
        return $failCount === 0 ? 0 : 1;
    }
}