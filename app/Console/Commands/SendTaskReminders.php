<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskReminder;

class SendTaskReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send pending task reminders';

    public function handle()
    {
        $tasks = Task::with('user')
            ->where('reminder_at', '<=', now())
            ->whereNull('reminded_at')
            ->where('is_done', false)
            ->get();

        $count = 0;
        
        foreach ($tasks as $task) {
            try {
                $task->user->notify(new TaskReminder($task));
                $task->update(['reminded_at' => now()]);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for task {$task->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} reminders");
        return Command::SUCCESS;
    }
}