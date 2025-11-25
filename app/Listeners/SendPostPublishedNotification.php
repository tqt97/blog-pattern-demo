<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Notifications\PostPublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPostPublishedNotification implements ShouldQueue
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
    public function handle(PostPublished $event): void
    {
        $post = $event->post;

        // Tùy logic, ở đây ví dụ gửi cho tất cả admin
        $admins = User::where('is_admin', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new PostPublishedNotification($post));
    }
}
