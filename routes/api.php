<?php

use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;

/**
 * API Routes - For external integrations and calendar features
 */

// Public API routes (no authentication required)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'device_name' => ['required', 'string'],
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user = Auth::user();
    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ]);
});

// Register
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'device_name' => ['required', 'string'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->filled('phone') ? $request->phone : null,
        'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 201);
});

// Forgot password - send reset link
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => ['required', 'email']]);

    $status = Password::sendResetLink($request->only('email'));

    if ($status != Password::RESET_LINK_SENT) {
        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => __('Password reset link sent to your email.'),
    ]);
});

// Google sign-in - exchange id_token for Sanctum token
Route::post('/auth/google', function (Request $request) {
    $request->validate([
        'id_token' => ['required', 'string'],
        'device_name' => ['required', 'string'],
    ]);

    $response = \Illuminate\Support\Facades\Http::get('https://oauth2.googleapis.com/tokeninfo', [
        'id_token' => $request->id_token,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid Google token.',
        ], 401);
    }

    $payload = $response->json();
    $email = $payload['email'] ?? null;
    $name = $payload['name'] ?? ($payload['email'] ?? 'User');
    $googleId = $payload['sub'] ?? null;

    if (!$email) {
        return response()->json([
            'success' => false,
            'message' => 'Email not provided by Google.',
        ], 401);
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(\Illuminate\Support\Str::random(32)),
            'email_verified_at' => now(),
        ]);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ]);
});

// Protected API routes (require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Get user information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Update user profile
    Route::put('/user', function (Request $request) {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $user = $request->user();
        $user->fill($validated);
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    });

    // Get user by phone number (for GekyChat integration)
    Route::get('/users/by-phone', function (Request $request) {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = \App\Models\User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this phone number'
            ], 404);
        }

        // Generate API token if not exists
        if (!$user->currentAccessToken()) {
            $token = $user->createToken('gekychat-integration')->plainTextToken;
        } else {
            $token = $user->currentAccessToken()->token;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'api_token' => $token
            ]
        ]);
    });

    // Get tasks (supports both calendar format and full format)
    Route::get('/tasks', function (Request $request) {
        $startDate = $request->query('start');
        $endDate = $request->query('end');
        $format = $request->query('format', 'full'); // 'full' or 'calendar'
        
        $query = $request->user()->tasks()->with('category');
        
        if ($startDate) {
            $query->where('task_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('task_date', '<=', $endDate);
        }
        
        $tasks = $query->get();
        
        // Return calendar format if requested
        if ($format === 'calendar') {
            return $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->task_date?->format('Y-m-d'),
                    'color' => $task->category->color ?? '#3b82f6',
                    'allDay' => true,
                    'extendedProps' => [
                        'priority' => $task->priority,
                        'is_done' => $task->is_done,
                        'category' => $task->category?->name,
                    ]
                ];
            });
        }
        
        // Return full task format (default for Flutter app)
        return $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'task_date' => $task->task_date?->format('Y-m-d'),
                'reminder_at' => $task->reminder_at?->toIso8601String(),
                'category_id' => $task->category_id,
                'priority' => $task->priority,
                'is_done' => $task->is_done,
                'user_id' => $task->user_id,
                'created_at' => $task->created_at?->toIso8601String(),
                'updated_at' => $task->updated_at?->toIso8601String(),
                'category' => $task->category ? [
                    'id' => $task->category->id,
                    'name' => $task->category->name,
                    'color' => $task->category->color,
                ] : null,
            ];
        });
    });

    // Get task statistics
    Route::get('/tasks/statistics', function (Request $request) {
        $user = $request->user();
        
        return [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('is_done', true)->count(),
            'pending' => $user->tasks()->where('is_done', false)->count(),
            'overdue' => $user->tasks()
                ->where('is_done', false)
                ->where('task_date', '<', today())
                ->count(),
        ];
    });

    // Get categories
    Route::get('/categories', function (Request $request) {
        return $request->user()->categories;
    });

    // Create category via API
    Route::post('/categories', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $category = $request->user()->categories()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    });

    // Update category via API
    Route::put('/categories/{category}', function (Request $request, Category $category) {
        // Ensure category belongs to authenticated user
        if ($category->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'color' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    });

    // Delete category via API
    Route::delete('/categories/{category}', function (Request $request, Category $category) {
        // Ensure category belongs to authenticated user
        if ($category->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if category has tasks
        if ($category->tasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing tasks'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    });

    // Create task via API (for external integrations)
    Route::post('/tasks', function (Request $request) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['nullable', 'integer', 'between:0,2'],
            'reminder_at' => ['nullable', 'date'],
        ]);

        // Set default task_date to today if not provided
        if (!isset($validated['task_date'])) {
            $validated['task_date'] = today()->toDateString();
        }

        // Set default priority if not provided
        if (!isset($validated['priority'])) {
            $validated['priority'] = 1; // Medium priority
        }

        $task = $request->user()->tasks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task->load('category')
        ], 201);
    });

    // Update task via API (for external integrations)
    Route::patch('/tasks/{task}', function (Request $request, \App\Models\Task $task) {
        // Ensure task belongs to authenticated user
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'task_date' => ['sometimes', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['sometimes', 'integer', 'between:0,2'],
            'reminder_at' => ['nullable', 'date'],
            'is_done' => ['sometimes', 'boolean'],
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task->load('category')
        ]);
    });

    // Delete task via API (for external integrations)
    Route::delete('/tasks/{task}', function (Request $request, \App\Models\Task $task) {
        // Ensure task belongs to authenticated user
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    });
});