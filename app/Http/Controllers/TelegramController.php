<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    /**
     * Handle Telegram webhook.
     */
    public function webhook(Request $request): JsonResponse
    {
        $update = $request->all();

        Log::info('Telegram webhook received', $update);

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';

            // Handle /start command
            if (str_starts_with($text, '/start')) {
                $parts = explode(' ', $text);

                if (count($parts) === 2 && str_starts_with($parts[1], 'setup_')) {
                    $userId = str_replace('setup_', '', $parts[1]);

                    $user = User::find($userId);
                    if ($user) {
                        $user->update([
                            'telegram_chat_id' => $chatId,
                            'telegram_notifications' => true,
                        ]);

                        $this->sendMessage($chatId, "✅ Successfully connected to BLACKTASK!\n\nYou'll now receive task reminders here.");

                        return response()->json(['status' => 'connected']);
                    }
                }

                // Default start message
                $this->sendMessage($chatId, "👋 Welcome to BLACKTASK!\n\nUse /start setup_{your_user_id} to connect your account.");
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Setup Telegram for a user.
     */
    public function setup(Request $request): JsonResponse
    {
        $user = auth()->user();

        $botInfo = app(\App\Services\NotificationService::class)->getTelegramBotInfo();

        if (!$botInfo || !isset($botInfo['result']['username'])) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram bot is not configured.'
            ]);
        }

        $botUsername = $botInfo['result']['username'];

        return response()->json([
            'success' => true,
            'bot_username' => $botUsername,
            'setup_url' => "https://t.me/{$botUsername}?start=setup_{$user->id}"
        ]);
    }

    /**
     * Disconnect Telegram.
     */
    public function disconnect(Request $request): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            'telegram_chat_id' => null,
            'telegram_notifications' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Telegram disconnected successfully.'
        ]);
    }

    /**
     * Send a message via Telegram API.
     */
    private function sendMessage(string $chatId, string $text): bool
    {
        $botToken = config('services.telegram.bot_token');
        $apiUrl = config('services.telegram.api_url');

        if (!$botToken) {
            return false;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::post("{$apiUrl}{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
