<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'browser_notifications',
        'email_notifications',
        'whatsapp_notifications',
        'sms_notifications',
        'gekychat_notifications',
        'push_notifications',
        'telegram_notifications',
        'telegram_chat_id',
        'push_token',
        'notification_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'browser_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'gekychat_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'telegram_notifications' => 'boolean',
        ];
    }
     public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // app/Models/User.php
public function categories()
{
    return $this->hasMany(Category::class);
}
}
