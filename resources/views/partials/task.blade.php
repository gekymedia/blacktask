<li id="task-{{ $task->id }}" class="flex items-center justify-between p-3 border border-gray-300 rounded {{ $task->is_done ? 'line-through text-gray-500' : '' }}">
    <div class="flex items-center space-x-3">
        <!-- Completion checkbox -->
        <input type="checkbox" class="toggle-checkbox h-4 w-4 text-blue-600" data-id="{{ $task->id }}" {{ $task->is_done ? 'checked' : '' }}>
        <!-- Task title -->
        <span>{{ $task->title }}</span>
    </div>
    <div class="flex items-center space-x-2">
        <!-- Move to tomorrow button -->
        <button class="move-tomorrow text-sm text-yellow-500 hover:text-yellow-600" data-id="{{ $task->id }}" title="Move to tomorrow">Tomorrow</button>
        <!-- Delete button -->
        <button class="delete-task text-sm text-red-500 hover:text-red-600" data-id="{{ $task->id }}" title="Delete task">Delete</button>
    </div>
</li>