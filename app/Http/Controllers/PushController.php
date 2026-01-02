<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushController extends Controller
{
    /**
     * Store push subscription.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'subscription' => 'required|array',
            'subscription.endpoint' => 'required|url',
            'subscription.keys' => 'required|array',
            'subscription.keys.auth' => 'required|string',
            'subscription.keys.p256dh' => 'required|string',
        ]);

        $user = auth()->user();
        $user->update([
            'push_token' => json_encode($request->subscription),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Push subscription saved successfully.'
        ]);
    }

    /**
     * Remove push subscription.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->update([
            'push_token' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed successfully.'
        ]);
    }

    /**
     * Get VAPID public key for client-side subscription.
     */
    public function vapidPublicKey(Request $request): JsonResponse
    {
        $publicKey = config('services.push.vapid_public_key');

        if (!$publicKey) {
            return response()->json([
                'success' => false,
                'message' => 'VAPID keys not configured.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'public_key' => $publicKey
        ]);
    }
}
