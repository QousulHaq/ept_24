<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json($this->getNotifications());
    }

    public function readAll()
    {
        foreach ($this->getNotifications()->items() as $notification) {
            if ($notification instanceof DatabaseNotification) {
                $notification->markAsRead();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'all notifications marked as read!',
        ]);
    }

    private function getNotifications()
    {
        $query = $this->authenticatedUser()->notifications();

        $query->when(request('unread', false), function ($q) {
            $q->whereNull('read_at');
        });

        $query->orderBy('created_at', 'desc');

        return $query->paginate(request('per_page', 10));
    }
}
