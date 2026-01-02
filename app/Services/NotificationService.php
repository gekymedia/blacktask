<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

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

        // Push notification
        if ($user->push_notifications && $user->push_token) {
            try {
                $this->sendPushNotification($user, $task);
                $sent[] = 'push';
            } catch (\Exception $e) {
                Log::error('Push notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Telegram notification
        if ($user->telegram_notifications && $user->telegram_chat_id) {
            try {
                $this->sendTelegramNotification($user, $task);
                $sent[] = 'telegram';
            } catch (\Exception $e) {
                Log::error('Telegram notification failed', [
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
        $whatsappApiUrl = config('notifications.whatsapp.api_url');
        $whatsappToken = config('notifications.whatsapp.token');
        $phoneNumberId = config('notifications.whatsapp.phone_number_id');

        if (!$whatsappApiUrl || !$whatsappToken || !$phoneNumberId) {
            Log::warning('WhatsApp API not configured', [
                'api_url' => $whatsappApiUrl ? 'set' : 'missing',
                'token' => $whatsappToken ? 'set' : 'missing',
                'phone_number_id' => $phoneNumberId ? 'set' : 'missing',
            ]);
            return false;
        }

        if (!$user->phone) {
            Log::warning('User has no phone number for WhatsApp notification', [
                'user_id' => $user->id,
                'task_id' => $task->id,
            ]);
            return false;
        }

        $message = "🔔 *BLACKTASK Reminder*\n\n"
            . "📝 *Task:* {$task->title}\n"
            . "📅 *Due:* {$task->task_date->format('M j, Y')}\n"
            . "🚨 *Priority:* " . $this->getPriorityText($task->priority);

        if ($task->category) {
            $message .= "\n📂 *Category:* {$task->category->name}";
        }

        try {
            $url = "{$whatsappApiUrl}/{$phoneNumberId}/messages";
            $response = Http::withToken($whatsappToken)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $user->phone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'phone' => $user->phone,
                ]);
                return true;
            } else {
                Log::error('WhatsApp notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'response' => $response->body(),
                    'status' => $response->status(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification exception', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS notification.
     */
    protected function sendSMSNotification(User $user, Task $task): bool
    {
        $provider = config('notifications.sms.provider', 'twilio');
        $smsApiUrl = config('notifications.sms.api_url');
        $smsToken = config('notifications.sms.token');
        $fromNumber = config('notifications.sms.from');

        if (!$smsApiUrl || !$smsToken || !$fromNumber) {
            Log::warning('SMS API not configured', [
                'provider' => $provider,
                'api_url' => $smsApiUrl ? 'set' : 'missing',
                'token' => $smsToken ? 'set' : 'missing',
                'from' => $fromNumber ? 'set' : 'missing',
            ]);
            return false;
        }

        if (!$user->phone) {
            Log::warning('User has no phone number for SMS notification', [
                'user_id' => $user->id,
                'task_id' => $task->id,
            ]);
            return false;
        }

        $message = "BLACKTASK: '{$task->title}' is due {$task->task_date->format('M j')}. Priority: {$this->getPriorityText($task->priority)}";

        try {
            $payload = [];

            // Different payload formats for different providers
            switch ($provider) {
                case 'twilio':
                    $payload = [
                        'To' => $user->phone,
                        'From' => $fromNumber,
                        'Body' => $message,
                    ];
                    break;
                case 'nexmo':
                    $payload = [
                        'to' => $user->phone,
                        'from' => $fromNumber,
                        'text' => $message,
                    ];
                    break;
                default:
                    $payload = [
                        'to' => $user->phone,
                        'from' => $fromNumber,
                        'message' => $message,
                    ];
            }

            $response = Http::withBasicAuth($provider === 'twilio' ? '' : '', $smsToken)
                ->asForm()
                ->post($smsApiUrl, $payload);

            if ($response->successful()) {
                Log::info('SMS notification sent successfully', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'provider' => $provider,
                    'phone' => $user->phone,
                ]);
                return true;
            } else {
                Log::error('SMS notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'provider' => $provider,
                    'response' => $response->body(),
                    'status' => $response->status(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS notification exception', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send GeKyChat notification.
     */
    protected function sendGeKyChatNotification(User $user, Task $task): bool
    {
        $gekychatApiUrl = config('notifications.gekychat.api_url');
        $gekychatToken = config('notifications.gekychat.token');

        if (!$gekychatApiUrl || !$gekychatToken) {
            Log::warning('GeKyChat API not configured', [
                'api_url' => $gekychatApiUrl ? 'set' : 'missing',
                'token' => $gekychatToken ? 'set' : 'missing',
            ]);
            return false;
        }

        if (!$user->phone) {
            Log::warning('User has no phone number for GeKyChat notification', [
                'user_id' => $user->id,
                'task_id' => $task->id,
            ]);
            return false;
        }

        $message = [
            'title' => '🔔 BLACKTASK Reminder',
            'body' => $task->title,
            'details' => [
                'due_date' => $task->task_date->format('M j, Y'),
                'priority' => $this->getPriorityText($task->priority),
                'category' => $task->category ? $task->category->name : null,
            ],
        ];

        try {
            $response = Http::withToken($gekychatToken)
                ->post($gekychatApiUrl, [
                    'user_id' => $user->phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('GeKyChat notification sent successfully', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'phone' => $user->phone,
                ]);
                return true;
            } else {
                Log::error('GeKyChat notification failed', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'response' => $response->body(),
                    'status' => $response->status(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('GeKyChat notification exception', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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
     * Send push notification using web push API.
     */
    protected function sendPushNotification(User $user, Task $task): bool
    {
        $vapidPublicKey = config('services.push.vapid_public_key');
        $vapidPrivateKey = config('services.push.vapid_private_key');

        if (!$vapidPublicKey || !$vapidPrivateKey || !$user->push_token) {
            Log::warning('Push notification not configured or user has no push token');
            return false;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.push.subject'),
                    'publicKey' => $vapidPublicKey,
                    'privateKey' => $vapidPrivateKey,
                ],
            ]);

            $subscription = Subscription::create(json_decode($user->push_token, true));

            $payload = $this->getBrowserNotificationPayload($task);
            $payload['body'] = "⏰ Task Due: {$task->title}";

            $result = $webPush->sendOneNotification(
                $subscription,
                json_encode($payload)
            );

            return $result->isSuccess();
        } catch (\Exception $e) {
            Log::error('Web push notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send Telegram notification.
     */
    protected function sendTelegramNotification(User $user, Task $task): bool
    {
        $botToken = config('services.telegram.bot_token');
        $apiUrl = config('services.telegram.api_url');

        if (!$botToken || !$user->telegram_chat_id) {
            Log::warning('Telegram API not configured or user has no chat ID');
            return false;
        }

        $message = "🔔 *BLACKTASK Reminder*\n\n"
            . "📝 *Task:* {$task->title}\n"
            . "📅 *Due:* {$task->task_date->format('M j, Y')}\n"
            . "🚨 *Priority:* " . $this->getPriorityText($task->priority);

        if ($task->category) {
            $message .= "\n📂 *Category:* {$task->category->name}";
        }

        $url = "{$apiUrl}{$botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $user->telegram_chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        return $response->successful();
    }

    /**
     * Get Telegram bot info for setup.
     */
    public function getTelegramBotInfo(): ?array
    {
        $botToken = config('services.telegram.bot_token');
        $apiUrl = config('services.telegram.api_url');

        if (!$botToken) {
            return null;
        }

        $url = "{$apiUrl}{$botToken}/getMe";

        try {
            $response = Http::get($url);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Failed to get Telegram bot info', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate VAPID keys for web push notifications.
     */
    public static function generateVapidKeys(): array
    {
        $keys = \Minishlink\WebPush\VAPID::createVapidKeys();
        return [
            'public_key' => $keys['publicKey'],
            'private_key' => $keys['privateKey'],
        ];
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

