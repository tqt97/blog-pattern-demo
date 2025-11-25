<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostPublishedNotification extends Notification implements ShouldQueue
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
            ->subject('Bài viết đã được publish: '.$this->post->title)
            ->greeting('Chào bạn,')
            ->line('Một bài viết vừa được publish.')
            ->line('Tiêu đề: '.$this->post->title)
            ->line('Thời gian publish: '.optional($this->post->published_at)->toDateTimeString())
            ->action('Xem trên frontend', route('blog.show', $this->post->slug)) // tuỳ route frontend của anh
            ->line('Bạn có thể kiểm tra lại nội dung, SEO, và chia sẻ bài viết.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'published_at' => $this->post->published_at,
            'admin_url' => route('admin.posts.edit', $this->post->id),
            'public_url' => route('blog.show', $this->post->slug),
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
