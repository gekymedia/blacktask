<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-primary-light dark:text-primary-dark">
            <i class="fas fa-cog mr-2"></i> Settings
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Notification Settings Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center mb-6">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 mr-4">
                    <i class="fas fa-bell text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold">Notification Preferences</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Choose how you want to receive task reminders</p>
                </div>
            </div>

            <form id="notification-settings-form">
                @csrf

                <!-- Browser Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-blue-600 dark:text-blue-400">
                            <i class="fas fa-desktop text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Browser Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Get notified in your browser</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="browser_notifications" class="sr-only peer" {{ $user->browser_notifications ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Email Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-green-600 dark:text-green-400">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Email Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Receive reminders via email ({{ $user->email }})</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="email_notifications" class="sr-only peer" {{ $user->email_notifications ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- WhatsApp Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-green-500 dark:text-green-400">
                            <i class="fab fa-whatsapp text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">WhatsApp Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Get reminders on WhatsApp
                                @if($user->phone)
                                    ({{ $user->phone }})
                                @else
                                    <a href="{{ route('profile.edit') }}" class="text-blue-600 underline">Add phone number</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="whatsapp_notifications" class="sr-only peer" {{ $user->whatsapp_notifications ? 'checked' : '' }} {{ !$user->phone ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 {{ !$user->phone ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                    </label>
                </div>

                <!-- SMS Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-purple-600 dark:text-purple-400">
                            <i class="fas fa-sms text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">SMS Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Receive text messages
                                @if($user->phone)
                                    ({{ $user->phone }})
                                @else
                                    <a href="{{ route('profile.edit') }}" class="text-blue-600 underline">Add phone number</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ $user->sms_notifications ? 'checked' : '' }} {{ !$user->phone ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 {{ !$user->phone ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                    </label>
                </div>

                <!-- GeKyChat Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-orange-600 dark:text-orange-400">
                            <i class="fas fa-comment text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">GeKyChat Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Get notified on GeKyChat
                                @if($user->phone)
                                    ({{ $user->phone }})
                                @else
                                    <a href="{{ route('profile.edit') }}" class="text-blue-600 underline">Add phone number</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="gekychat_notifications" class="sr-only peer" {{ $user->gekychat_notifications ? 'checked' : '' }} {{ !$user->phone ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 {{ !$user->phone ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                    </label>
                </div>

                <!-- Push Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-red-600 dark:text-red-400">
                            <i class="fas fa-mobile-alt text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Push Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Receive push notifications on mobile devices</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="push_notifications" class="sr-only peer" {{ $user->push_notifications ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Telegram Notifications -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <div class="mr-4 text-blue-500 dark:text-blue-400">
                            <i class="fab fa-telegram-plane text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Telegram Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Get notified on Telegram
                                @if($user->telegram_chat_id)
                                    (Connected)
                                @else
                                    <a href="#" id="telegram-setup" class="text-blue-600 underline">Setup Telegram</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="telegram_notifications" class="sr-only peer" {{ $user->telegram_notifications ? 'checked' : '' }} {{ !$user->telegram_chat_id ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 {{ !$user->telegram_chat_id ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                    </label>
                </div>

                <!-- Notification Time -->
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-start">
                        <div class="mr-4 text-indigo-600 dark:text-indigo-400">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Daily Reminder Time</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">When to send daily task reminders</p>
                        </div>
                    </div>
                    <input type="time" name="notification_time" value="{{ $user->notification_time ? \Carbon\Carbon::parse($user->notification_time)->format('H:i') : '09:00' }}" 
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark">
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-light dark:bg-primary-dark text-white rounded-lg hover:bg-opacity-90 transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Profile Settings Link -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 mr-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Profile Settings</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Update your personal information</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#notification-settings-form').submit(function(e) {
                e.preventDefault();
                
                const formData = {
                    browser_notifications: $('[name="browser_notifications"]').is(':checked'),
                    email_notifications: $('[name="email_notifications"]').is(':checked'),
                    whatsapp_notifications: $('[name="whatsapp_notifications"]').is(':checked'),
                    sms_notifications: $('[name="sms_notifications"]').is(':checked'),
                    gekychat_notifications: $('[name="gekychat_notifications"]').is(':checked'),
                    push_notifications: $('[name="push_notifications"]').is(':checked'),
                    telegram_notifications: $('[name="telegram_notifications"]').is(':checked'),
                    notification_time: $('[name="notification_time"]').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route('settings.notifications.update') }}',
                    method: 'PATCH',
                    data: formData,
                    success: function(response) {
                        // Show success notification
                        const notification = $('<div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">')
                            .html('<i class="fas fa-check-circle mr-2"></i>' + response.message)
                            .appendTo('body');
                        
                        setTimeout(() => notification.fadeOut(() => notification.remove()), 3000);
                    },
                    error: function(xhr) {
                        const notification = $('<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">')
                            .html('<i class="fas fa-exclamation-circle mr-2"></i>Failed to save settings')
                            .appendTo('body');
                        
                        setTimeout(() => notification.fadeOut(() => notification.remove()), 3000);
                    }
                });
            });

            // Enable browser notifications when toggled on
            $('[name="browser_notifications"]').change(function() {
                if ($(this).is(':checked') && 'Notification' in window) {
                    Notification.requestPermission();
                }
            });

            // Telegram setup
            $('#telegram-setup').click(function(e) {
                e.preventDefault();

                const botInfo = @json(app(\App\Services\NotificationService::class)->getTelegramBotInfo());

                if (botInfo && botInfo.result && botInfo.result.username) {
                    const botUsername = botInfo.result.username;
                    const setupUrl = `https://t.me/${botUsername}?start=setup_${{ auth()->id() }}`;

                    window.open(setupUrl, '_blank');
                } else {
                    alert('Telegram bot is not configured. Please contact administrator.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

