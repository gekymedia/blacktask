<!DOCTYPE html>
<html lang="en" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLACKTASK - Daily Task Planner</title>
    
    <!-- Using Tailwind via CDN for development (as in your original) -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Using alternative date parser that works -->
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    
    <style>
        .task-item {
            transition: all 0.2s ease;
        }
        .task-item:hover {
            transform: translateY(-2px);
        }
        .completed {
            text-decoration: line-through;
            opacity: 0.7;
        }
        .notification {
            transition: all 0.3s ease;
            transform: translateY(20px);
            opacity: 0;
        }
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen transition-colors duration-300">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-primary-light dark:text-primary-dark">
                <i class="fas fa-tasks mr-2"></i> BLACKTASK
            </h1>
            <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </header>

        <!-- Today's Date -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold">
                <i class="far fa-calendar-alt mr-2"></i> Today - {{ now()->format('l, F j, Y') }}
            </h2>
        </div>

        <!-- Add Task Form -->
        <form id="add-task-form" class="mb-8">
            <div class="flex gap-2 mb-4">
                <input
                    type="text"
                    id="task-input"
                    placeholder="Add a new task..."
                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors"
                    required>
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary-light dark:bg-primary-dark text-white rounded-lg hover:bg-opacity-90 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2">
                        @forelse(auth()->user()->categories ?? [] as $category)
                            <option value="{{ $category->id }}" style="color: {{ $category->color }}">
                                {{ $category->name }}
                            </option>
                        @empty
                            <option value="">No categories</option>
                        @endforelse
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Priority</label>
                    <select name="priority" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2">
                        <option value="0">Low</option>
                        <option value="1">Medium</option>
                        <option value="2" selected>High</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Task List -->
        <div id="task-list" class="space-y-3">
            @foreach($tasks as $task)
            <div
                id="task-{{ $task->id }}"
                class="task-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all flex items-center justify-between border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded focus:ring-primary-light dark:focus:ring-primary-dark border-gray-300 dark:border-gray-600 cursor-pointer"
                        data-task-id="{{ $task->id }}"
                        {{ $task->is_done ? 'checked' : '' }}>
                    <span class="ml-3 {{ $task->is_done ? 'completed' : '' }}">{{ $task->title }}</span>
                </div>
                <div class="flex gap-2">
                    <button
                        class="reschedule-btn p-2 text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors"
                        data-task-id="{{ $task->id }}"
                        title="Move to tomorrow">
                        <i class="far fa-calendar-plus"></i>
                    </button>
                    <button
                        class="delete-btn p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-gray-700 transition-colors"
                        data-task-id="{{ $task->id }}"
                        title="Delete task">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            @endforeach

            @if($tasks->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="far fa-smile-beam text-4xl mb-2"></i>
                <p class="text-lg">No tasks for today! Add one above.</p>
            </div>
            @endif
        </div>

        <!-- Notification -->
        <div id="notification" class="notification fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden">
            Task moved to tomorrow!
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Theme handling
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            }

            $('#theme-toggle').click(function() {
                $('html').toggleClass('dark');
                localStorage.setItem('dark-mode', $('html').hasClass('dark'));
            });

            // Date parsing with Luxon (replaces chrono)
            function parseNaturalLanguage(input) {
                // Try to parse simple date patterns
                const today = luxon.DateTime.now();
                const tomorrow = today.plus({ days: 1 });
                
                const patterns = [
                    { test: /today/i, date: today },
                    { test: /tomorrow/i, date: tomorrow },
                    { test: /next week/i, date: today.plus({ weeks: 1 }) },
                    { test: /next month/i, date: today.plus({ months: 1 }) }
                ];
                
                let date = null;
                let title = input;
                
                for (const pattern of patterns) {
                    if (pattern.test.test(input)) {
                        date = pattern.date;
                        title = input.replace(pattern.test, '').trim();
                        break;
                    }
                }
                
                return { 
                    title: title || input,
                    date: date ? date.toJSDate() : null 
                };
            }

            // Add new task
            $('#add-task-form').submit(function(e) {
                e.preventDefault();
                const input = $('#task-input').val().trim();
                const parsed = parseNaturalLanguage(input);
                
                const taskData = {
                    title: parsed.title,
                    task_date: parsed.date ? parsed.date.toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
                    category_id: $('[name="category_id"]').val(),
                    priority: $('[name="priority"]').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('tasks.store') }}",
                    method: 'POST',
                    data: taskData,
                    success: function(response) {
                        $('#task-input').val('');
                        const taskHtml = `
                            <div id="task-${response.id}" class="task-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all flex items-center justify-between border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <input type="checkbox" class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded focus:ring-primary-light dark:focus:ring-primary-dark border-gray-300 dark:border-gray-600 cursor-pointer" data-task-id="${response.id}">
                                    <span class="ml-3">${response.title}</span>
                                </div>
                                <div class="flex gap-2">
                                    <button class="reschedule-btn p-2 text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${response.id}" title="Move to tomorrow">
                                        <i class="far fa-calendar-plus"></i>
                                    </button>
                                    <button class="delete-btn p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${response.id}" title="Delete task">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        `;

                        if ($('#task-list').find('.text-center').length) {
                            $('#task-list').html(taskHtml);
                        } else {
                            $('#task-list').prepend(taskHtml);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            // Toggle task completion
            $(document).on('change', '.task-checkbox', function() {
                const taskId = $(this).data('task-id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    method: 'PATCH',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function() {
                        $(`#task-${taskId} span`).toggleClass('completed', isChecked);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            // Move task to tomorrow
            $(document).on('click', '.reschedule-btn', function() {
                const taskId = $(this).data('task-id');

                $.ajax({
                    url: `/tasks/${taskId}/reschedule`,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'POST'
                    },
                    success: function() {
                        $(`#task-${taskId}`).fadeOut(300, function() {
                            $(this).remove();

                            // Show notification
                            $('#notification').removeClass('hidden').addClass('show');
                            setTimeout(() => {
                                $('#notification').removeClass('show').addClass('hidden');
                            }, 3000);

                            if ($('#task-list').children().length === 0) {
                                $('#task-list').html(`
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <i class="far fa-smile-beam text-4xl mb-2"></i>
                                        <p class="text-lg">No tasks for today! Add one above.</p>
                                    </div>
                                `);
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            // Delete task
            $(document).on('click', '.delete-btn', function() {
                const taskId = $(this).data('task-id');

                if (confirm('Are you sure you want to delete this task?')) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function() {
                            $(`#task-${taskId}`).fadeOut(300, function() {
                                $(this).remove();

                                if ($('#task-list').children().length === 0) {
                                    $('#task-list').html(`
                                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                            <i class="far fa-smile-beam text-4xl mb-2"></i>
                                            <p class="text-lg">No tasks for today! Add one above.</p>
                                        </div>
                                    `);
                                }
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>