<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'read',
        'read_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método estático para crear notificación
    public static function createFor($userId, $type, $title, $message, $link = null)
    {
        $notification = self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);

        // Disparar evento para notificación en tiempo real
        broadcast(new \App\Events\NotificationCreated($notification));

        return $notification;
    }

    // Marcar como leída
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }
}
