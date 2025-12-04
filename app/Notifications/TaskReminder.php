<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Or other channels
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Reminder: {$this->task->title}")
            ->line("This is a reminder for your task: {$this->task->title}")
            ->action('View Task', url("/tasks/{$this->task->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'url' => url("/tasks/{$this->task->id}")
        ];
    }
}