<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary-light dark:text-primary-dark">
                <i class="fas fa-tasks mr-2"></i> BLACKTASK Dashboard
            </h1>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Welcome Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-primary-light dark:bg-primary-dark text-white mr-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Welcome back, {{ Auth::user()->name }}!</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>
                <button id="request-notifications" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-bell mr-2"></i> Enable Notifications
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Today's Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Tasks</h3>
                        <p class="text-3xl font-bold">{{ $todayTasks->count() }}</p>
                        <p class="text-xs text-gray-500">{{ $todayUndone->count() }} pending</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Tomorrow's Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tomorrow</h3>
                        <p class="text-3xl font-bold">{{ $tomorrowTasks->count() }}</p>
                        <p class="text-xs text-gray-500">{{ $tomorrowTasks->where('is_done', false)->count() }} pending</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                        <i class="fas fa-calendar-plus text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed Today</h3>
                        <p class="text-3xl font-bold">{{ $todayTasks->where('is_done', true)->count() }}</p>
                        @php
                            $completion = $todayTasks->count() > 0 ? round(($todayTasks->where('is_done', true)->count() / $todayTasks->count()) * 100) : 0;
                        @endphp
                        <p class="text-xs text-gray-500">{{ $completion }}% done</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue</h3>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $overdueTasks->count() }}</p>
                        <p class="text-xs text-gray-500">needs attention</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Today's Undone Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                        Today's Pending Tasks
                    </h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($todayUndone as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <input type="checkbox" class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded" data-task-id="{{ $task->id }}">
                                <div class="flex-1">
                                    <p class="font-medium">{{ $task->title }}</p>
                                    @if($task->category)
                                        <p class="text-xs" style="color: {{ $task->category->color }}">
                                            <i class="fas fa-tag"></i> {{ $task->category->name }}
                                        </p>
                                    @endif
                                </div>
                                @if($task->priority === 2)
                                    <span class="text-red-500" title="High Priority">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i>
                            <p>All tasks completed for today! 🎉</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tomorrow's Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-calendar-day mr-2 text-purple-600"></i>
                        Tomorrow's Tasks
                    </h3>
                    <span class="text-sm text-gray-500">{{ now()->addDay()->format('M j') }}</span>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($tomorrowTasks as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <input type="checkbox" class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded" data-task-id="{{ $task->id }}" {{ $task->is_done ? 'checked' : '' }}>
                                <div class="flex-1 {{ $task->is_done ? 'opacity-50' : '' }}">
                                    <p class="font-medium {{ $task->is_done ? 'line-through' : '' }}">{{ $task->title }}</p>
                                    @if($task->category)
                                        <p class="text-xs" style="color: {{ $task->category->color }}">
                                            <i class="fas fa-tag"></i> {{ $task->category->name }}
                                        </p>
                                    @endif
                                </div>
                                @if($task->priority === 2)
                                    <span class="text-red-500" title="High Priority">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="far fa-calendar text-4xl mb-2"></i>
                            <p>No tasks scheduled for tomorrow</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Overdue Tasks Alert -->
        @if($overdueTasks->count() > 0)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-300">Overdue Tasks</h3>
                    <p class="text-sm text-red-700 dark:text-red-400 mt-1">You have {{ $overdueTasks->count() }} overdue task(s) that need your attention.</p>
                    <div class="mt-4 space-y-2">
                        @foreach($overdueTasks->take(3) as $task)
                            <div class="flex items-center text-sm text-red-800 dark:text-red-300">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                <span>{{ $task->title }}</span>
                                <span class="ml-2 text-xs text-red-600">({{ $task->task_date->diffForHumans() }})</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('tasks.index') }}" class="inline-block mt-3 text-sm text-red-700 dark:text-red-400 underline hover:text-red-900">
                        View all overdue tasks →
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('tasks.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-primary-light dark:bg-primary-dark text-white mr-4">
                        <i class="fas fa-plus text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Create New Task</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add a task to your list</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('calendar.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 mr-4">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Calendar View</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">See all your tasks</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('settings.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mr-4">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Settings</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Manage notifications</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Request browser notification permission
            $('#request-notifications').click(function() {
                if ('Notification' in window) {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === 'granted') {
                            new Notification('BLACKTASK Notifications Enabled!', {
                                body: 'You will now receive reminders for your tasks.',
                                icon: '/favicon.ico'
                            });
                            $(this).html('<i class="fas fa-check mr-2"></i> Notifications Enabled');
                            $(this).removeClass('bg-blue-500 hover:bg-blue-600').addClass('bg-green-500');
                        }
                    });
                }
            });

            // Check if notifications are already enabled
            if ('Notification' in window && Notification.permission === 'granted') {
                $('#request-notifications').html('<i class="fas fa-check mr-2"></i> Notifications Enabled');
                $('#request-notifications').removeClass('bg-blue-500 hover:bg-blue-600').addClass('bg-green-500');
            }

            // Task checkbox toggle
            $('.task-checkbox').change(function() {
                const taskId = $(this).data('task-id');
                const isChecked = $(this).is(':checked');
                const checkbox = $(this);

                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PATCH'
                    },
                    success: function() {
                        checkbox.closest('.flex').toggleClass('opacity-50', isChecked);
                        checkbox.siblings('div').find('p').first().toggleClass('line-through', isChecked);
                        
                        // Reload after 500ms to update stats
                        setTimeout(() => location.reload(), 500);
                    },
                    error: function() {
                        checkbox.prop('checked', !isChecked);
                        alert('Failed to update task');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>