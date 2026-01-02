<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {user? : User ID or email to test with} {--channel= : Specific channel to test (browser,email,whatsapp,sms,gekychat,push,telegram)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification channels with a sample task';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $specificChannel = $this->option('channel');

        // Find user
        if (!$userId) {
            $user = User::first();
            if (!$user) {
                $this->error('No users found. Please create a user first.');
                return 1;
            }
            $this->info("Using first user: {$user->email}");
        } elseif (is_numeric($userId)) {
            $user = User::find($userId);
        } else {
            $user = User::where('email', $userId)->first();
        }

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        // Create a test task
        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Test Notification Task',
            'task_date' => now()->addDay(),
            'priority' => 2,
            'is_done' => false,
        ]);

        $this->info("Created test task: '{$task->title}' due on {$task->task_date->format('M j, Y')}");

        $notificationService = app(NotificationService::class);

        if ($specificChannel) {
            // Test specific channel
            $this->testSpecificChannel($user, $task, $notificationService, $specificChannel);
        } else {
            // Test all channels
            $this->testAllChannels($user, $task, $notificationService);
        }

        // Clean up test task
        $task->delete();
        $this->info('Test task cleaned up.');

        return 0;
    }

    /**
     * Test a specific notification channel.
     */
    private function testSpecificChannel(User $user, Task $task, NotificationService $service, string $channel): void
    {
        $this->info("Testing {$channel} notification...");

        // Temporarily enable the specific channel
        $originalValue = $user->{$channel . '_notifications'};
        $user->update([$channel . '_notifications' => true]);

        $sent = $service->sendTaskReminder($user, $task);

        // Restore original value
        $user->update([$channel . '_notifications' => $originalValue]);

        if (in_array($channel, $sent)) {
            $this->info("✅ {$channel} notification sent successfully!");
        } else {
            $this->error("❌ {$channel} notification failed. Check logs for details.");
        }
    }

    /**
     * Test all notification channels.
     */
    private function testAllChannels(User $user, Task $task, NotificationService $service): void
    {
        $this->info('Testing all notification channels...');
        $this->line('');

        $channels = ['browser', 'email', 'whatsapp', 'sms', 'gekychat', 'push', 'telegram'];
        $results = [];

        foreach ($channels as $channel) {
            $this->info("Testing {$channel}...");

            // Temporarily enable the channel
            $originalValue = $user->{$channel . '_notifications'};
            $user->update([$channel . '_notifications' => true]);

            $sent = $service->sendTaskReminder($user, $task);

            // Restore original value
            $user->update([$channel . '_notifications' => $originalValue]);

            $results[$channel] = in_array($channel, $sent);
            $status = $results[$channel] ? '✅' : '❌';
            $this->info("  {$status} {$channel}: " . ($results[$channel] ? 'Success' : 'Failed'));
        }

        $this->line('');
        $this->info('Summary:');
        foreach ($results as $channel => $success) {
            $status = $success ? 'Working' : 'Not configured or failed';
            $this->info("  {$channel}: {$status}");
        }

        $workingCount = count(array_filter($results));
        $this->info("Total working channels: {$workingCount}/" . count($channels));
    }
}
