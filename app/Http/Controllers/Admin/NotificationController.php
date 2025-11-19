<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $query = auth()->user()->notifications()
            ->orderBy('created_at', 'DESC');

        // Filtro para mostrar solo no leídas
        if (request('filter') == 'unread') {
            $query->where('read', false);
        }

        $notifications = $query->paginate(20);
        $unreadCount = auth()->user()->notifications()->where('read', false)->count();
        $total = auth()->user()->notifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'total'));
    }

    public function unread()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
