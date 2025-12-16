@props(['task'])

<div
    id="task-{{ $task->id }}"
    class="task-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all flex items-center justify-between border border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-3">
        <input
            type="checkbox"
            class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded focus:ring-primary-light dark:focus:ring-primary-dark border-gray-300 dark:border-gray-600 cursor-pointer"
            data-task-id="{{ $task->id }}"
            {{ $task->is_done ? 'checked' : '' }}>
        
        <div class="flex flex-col">
            <span class="ml-0 {{ $task->is_done ? 'completed' : '' }}">{{ $task->title }}</span>
            @if($task->category)
                <span class="text-xs mt-1" style="color: {{ $task->category->color }}">
                    <i class="fas fa-tag"></i> {{ $task->category->name }}
                </span>
            @endif
        </div>
    </div>
    
    <div class="flex gap-2 items-center">
        @if($task->priority === 2)
            <span class="text-red-500" title="High Priority">
                <i class="fas fa-exclamation-circle"></i>
            </span>
        @elseif($task->priority === 1)
            <span class="text-yellow-500" title="Medium Priority">
                <i class="fas fa-circle"></i>
            </span>
        @endif
        
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

