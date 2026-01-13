<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BLACKTASK</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('web_pwa/icon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('web_pwa/icon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('web_pwa/icon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web_pwa/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('web_pwa/icon-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('web_pwa/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('web_pwa/icon-512x512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#4f46e5',
                            dark: '#6366f1'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen transition-colors duration-300">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <!-- Header -->
        <header class="flex flex-col justify-center items-center mb-8">
            <img src="{{ asset('web_pwa/icon-192x192.png') }}" alt="BlackTask Logo" class="w-20 h-20 mb-4">
            <h1 class="text-3xl font-bold text-primary-light dark:text-primary-dark">
                BLACKTASK
            </h1>
        </header>

        <!-- Register Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold mb-6 text-center">Create Account</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium mb-1">Phone Number (Optional)</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1234567890"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors">
                    <p class="mt-1 text-xs text-gray-500">For SMS and WhatsApp notifications</p>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors">
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-primary-light dark:text-primary-dark hover:underline">
                        Already registered?
                    </a>

                    <button type="submit" class="px-4 py-2 bg-primary-light dark:bg-primary-dark text-white rounded-lg hover:bg-opacity-90 transition-colors">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Theme handling if needed
        if (localStorage.getItem('dark-mode') === 'true' || 
            (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('dark-mode', 'true');
        }
    </script>
</body>
</html>