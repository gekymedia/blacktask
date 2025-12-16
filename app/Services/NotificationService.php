<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send task reminder notifications to user based on their preferences.
     */
    public function sendTaskReminder(User $user, Task $task): array
    {
        $sent = [];

        // Browser notification (handled by frontend)
        if ($user->browser_notifications) {
            $sent[] = 'browser';
        }

        // Email notification
        if ($user->email_notifications) {
            try {
                $user->notify(new TaskReminderNotification($task));
                $sent[] = 'email';
            } catch (\Exception $e) {
                Log::error('Email notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // WhatsApp notification
        if ($user->whatsapp_notifications && $user->phone) {
            try {
                $this->sendWhatsAppNotification($user, $task);
                $sent[] = 'whatsapp';
            } catch (\Exception $e) {
                Log::error('WhatsApp notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // SMS notification
        if ($user->sms_notifications && $user->phone) {
            try {
                $this->sendSMSNotification($user, $task);
                $sent[] = 'sms';
            } catch (\Exception $e) {
                Log::error('SMS notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // GeKyChat notification
        if ($user->gekychat_notifications && $user->phone) {
            try {
                $this->sendGeKyChatNotification($user, $task);
                $sent[] = 'gekychat';
            } catch (\Exception $e) {
                Log::error('GeKyChat notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $sent;
    }

    /**
     * Send daily digest to users with pending tasks.
     */
    public function sendDailyDigest(User $user, Collection $tasks): array
    {
        $sent = [];

        if ($tasks->isEmpty()) {
            return $sent;
        }

        // Email digest
        if ($user->email_notifications) {
            try {
                $user->notify(new \App\Notifications\DailyDigestNotification($tasks));
                $sent[] = 'email';
            } catch (\Exception $e) {
                Log::error('Email digest failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Other notification channels can be added here
        
        return $sent;
    }

    /**
     * Send WhatsApp notification.
     */
    protected function sendWhatsAppNotification(User $user, Task $task): bool
    {
        // Integration with WhatsApp Business API
        // You'll need to set up WhatsApp Business API credentials
        $whatsappApiUrl = config('services.whatsapp.api_url');
        $whatsappToken = config('services.whatsapp.token');

        if (!$whatsappApiUrl || !$whatsappToken) {
            Log::warning('WhatsApp API not configured');
            return false;
        }

        $message = "🔔 BLACKTASK Reminder\n\n"
            . "Task: {$task->title}\n"
            . "Due: {$task->task_date->format('M j, Y')}\n"
            . "Priority: " . $this->getPriorityText($task->priority);

        $response = Http::withToken($whatsappToken)
            ->post($whatsappApiUrl, [
                'phone' => $user->phone,
                'message' => $message,
            ]);

        return $response->successful();
    }

    /**
     * Send SMS notification.
     */
    protected function sendSMSNotification(User $user, Task $task): bool
    {
        // Integration with SMS service (Twilio, Nexmo, etc.)
        $smsApiUrl = config('services.sms.api_url');
        $smsToken = config('services.sms.token');

        if (!$smsApiUrl || !$smsToken) {
            Log::warning('SMS API not configured');
            return false;
        }

        $message = "BLACKTASK: '{$task->title}' is due {$task->task_date->format('M j')}. "
            . "Priority: " . $this->getPriorityText($task->priority);

        $response = Http::withToken($smsToken)
            ->post($smsApiUrl, [
                'to' => $user->phone,
                'message' => $message,
            ]);

        return $response->successful();
    }

    /**
     * Send GeKyChat notification.
     */
    protected function sendGeKyChatNotification(User $user, Task $task): bool
    {
        // Integration with GeKyChat API
        $gekychatApiUrl = config('services.gekychat.api_url');
        $gekychatToken = config('services.gekychat.token');

        if (!$gekychatApiUrl || !$gekychatToken) {
            Log::warning('GeKyChat API not configured');
            return false;
        }

        $message = [
            'title' => '🔔 BLACKTASK Reminder',
            'body' => $task->title,
            'details' => [
                'due_date' => $task->task_date->format('M j, Y'),
                'priority' => $this->getPriorityText($task->priority),
            ],
        ];

        $response = Http::withToken($gekychatToken)
            ->post($gekychatApiUrl, [
                'user_id' => $user->phone,
                'message' => $message,
            ]);

        return $response->successful();
    }

    /**
     * Get priority text.
     */
    protected function getPriorityText(int $priority): string
    {
        return match ($priority) {
            0 => 'Low',
            1 => 'Medium',
            2 => 'High',
            default => 'Medium',
        };
    }

    /**
     * Send browser notification payload.
     */
    public function getBrowserNotificationPayload(Task $task): array
    {
        return [
            'title' => '🔔 BLACKTASK Reminder',
            'body' => $task->title,
            'icon' => '/favicon.ico',
            'badge' => '/favicon.ico',
            'data' => [
                'task_id' => $task->id,
                'due_date' => $task->task_date->toDateString(),
                'priority' => $task->priority,
            ],
            'actions' => [
                [
                    'action' => 'complete',
                    'title' => 'Mark Complete',
                ],
                [
                    'action' => 'view',
                    'title' => 'View Task',
                ],
            ],
        ];
    }
}

