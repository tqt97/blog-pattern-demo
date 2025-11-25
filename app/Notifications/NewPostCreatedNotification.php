<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bài viết mới được tạo: '.$this->post->title)
            ->greeting('Chào admin,')
            ->line('Một bài viết mới vừa được tạo.')
            ->line('Tiêu đề: '.$this->post->title)
            ->action('Xem bài viết', route('admin.posts.edit', $this->post->id))
            ->line('Cảm ơn bạn đã quản trị hệ thống!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'created_at' => $this->post->created_at,
            'url' => route('admin.posts.edit', $this->post->id),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
