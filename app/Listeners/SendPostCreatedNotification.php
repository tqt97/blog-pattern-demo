<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\User;
use App\Notifications\NewPostCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPostCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;

        // Tuỳ hệ thống anh phân quyền như nào:
        // ví dụ đơn giản: user có is_admin = true
        // $admins = User::where('is_admin', true)->get();
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        Notification::send($users, new NewPostCreatedNotification($post));
    }
}
