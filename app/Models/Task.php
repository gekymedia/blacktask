<?php

// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'is_done',
        'task_date',
        'reminder_at',
        'category_id',
        'priority', 
        
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'task_date' => 'date',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
      /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
