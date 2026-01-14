<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Webhook Controller for external integrations
 * Notifies GekyChat when tasks are created/updated
 */
class WebhookController extends Controller
{
    /**
     * Notify GekyChat about task events
     */
    public function notifyGekyChat(string $event, Task $task)
    {
        $gekyc hatUrl = config('services.gekychat.url');
        $webhookToken = config('services.gekychat.webhook_token');

        if (!$gekychatUrl || !$webhookToken) {
            return; // GekyChat integration not configured
        }

        try {
            $payload = [
                'event' => $event, // 'task.created', 'task.completed', 'task.deleted'
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'task_date' => $task->task_date->toDateString(),
                    'priority' => $task->priority,
                    'is_done' => $task->is_done,
                    'category' => $task->category?->name,
                ],
                'user' => [
                    'id' => $task->user_id,
                    'phone' => $task->user->phone,
                    'name' => $task->user->name,
                ],
                'timestamp' => now()->toISOString(),
            ];

            Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $webhookToken,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$gekychatUrl}/api/webhooks/blacktask", $payload);

        } catch (\Exception $e) {
            Log::error('GekyChat webhook notification failed: ' . $e->getMessage());
        }
    }
}
