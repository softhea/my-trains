<?php

namespace App\Console\Commands;

use App\Mail\NewMessageNotification;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality with message notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email sending to: {$email}");
        
        try {
            // Get or create test users
            $sender = User::first();
            $receiver = User::where('email', $email)->first();
            
            if (!$sender) {
                $this->error('No users found in database. Please create at least one user first.');
                return 1;
            }
            
            if (!$receiver) {
                $this->warn("User with email {$email} not found. Creating a temporary user for testing.");
                $receiver = new User([
                    'name' => 'Test User',
                    'email' => $email,
                ]);
            }
            
            // Create a test message
            $message = new Message();
            $message->sender_id = $sender->id;
            $message->receiver_id = $receiver->id ?? 999;
            $message->subject = 'Test Message - Email Notification';
            $message->message = 'This is a test message to verify email notifications are working correctly.';
            $message->created_at = now();
            $message->updated_at = now();
            
            // Set relationships manually for testing
            $message->setRelation('sender', $sender);
            $message->setRelation('receiver', $receiver);
            $message->setRelation('product', null);
            
            $this->info('Attempting to send test email...');
            
            Mail::to($email)->send(new NewMessageNotification($message));
            
            $this->info('✅ Email sent successfully!');
            $this->info('Check the email inbox and server logs for confirmation.');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email:');
            $this->error($e->getMessage());
            $this->error('Check your email configuration and server logs for more details.');
            
            return 1;
        }
    }
}
