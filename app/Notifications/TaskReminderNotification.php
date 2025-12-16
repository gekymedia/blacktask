<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Task $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
        $priorityText = match ($this->task->priority) {
            0 => '🟢 Low Priority',
            1 => '🟡 Medium Priority',
            2 => '🔴 High Priority',
            default => 'Medium Priority',
        };

        $message = (new MailMessage)
            ->subject('🔔 Task Reminder: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder about your upcoming task:')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Due Date:** ' . $this->task->task_date->format('l, F j, Y'))
            ->line('**Priority:** ' . $priorityText);

        if ($this->task->category) {
            $message->line('**Category:** ' . $this->task->category->name);
        }

        $message->action('View Task', route('tasks.index'))
            ->line('Don\'t forget to complete your task!')
            ->line('Stay organized with BLACKTASK 📋');

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
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->task_date->toDateString(),
            'priority' => $this->task->priority,
        ];
    }
}

