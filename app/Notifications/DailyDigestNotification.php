<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DailyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $tasks;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('📋 Your Daily Task Digest - ' . now()->format('M j, Y'))
            ->greeting('Good morning, ' . $notifiable->name . '!')
            ->line('Here\'s your task summary for today:')
            ->line('**Total Tasks:** ' . $this->tasks->count())
            ->line('**Pending:** ' . $this->tasks->where('is_done', false)->count())
            ->line('**Completed:** ' . $this->tasks->where('is_done', true)->count())
            ->line('')
            ->line('**Today\'s Pending Tasks:**');

        $pendingTasks = $this->tasks->where('is_done', false);

        if ($pendingTasks->count() > 0) {
            foreach ($pendingTasks->take(10) as $task) {
                $priority = match ($task->priority) {
                    2 => '🔴',
                    1 => '🟡',
                    0 => '🟢',
                    default => '⚪',
                };
                $message->line($priority . ' ' . $task->title);
            }

            if ($pendingTasks->count() > 10) {
                $message->line('... and ' . ($pendingTasks->count() - 10) . ' more tasks');
            }
        } else {
            $message->line('✅ All tasks completed! Great job!');
        }

        $message->action('View All Tasks', route('dashboard'))
            ->line('Have a productive day! 🚀');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'total_tasks' => $this->tasks->count(),
            'pending' => $this->tasks->where('is_done', false)->count(),
            'completed' => $this->tasks->where('is_done', true)->count(),
        ];
    }
}

