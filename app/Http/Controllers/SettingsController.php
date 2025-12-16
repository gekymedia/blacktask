<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        return view('settings.index', compact('user'));
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'browser_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'gekychat_notifications' => 'boolean',
            'notification_time' => 'nullable|date_format:H:i',
        ]);

        $user = auth()->user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully!',
        ]);
    }
}

