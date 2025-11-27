<?php

namespace App\Http\Controllers;

use App\Actions\Post\PublishPostAction;
use App\Exceptions\PostException;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class AdminPostController extends Controller
{
    public function __construct(
        protected PublishPostAction $publishPostAction,
        // protected PostService $postService, // nếu anh vẫn dùng Service cho CRUD
    ) {}

    // ... index, create, store, edit, update, destroy

    /**
     * Publish bài viết (từ admin).
     */
    public function publish(Post $post): RedirectResponse
    {
        $this->authorize('publish', $post);
        try {
            // nếu cần set thời gian publish custom (ví dụ từ form), truyền thêm Carbon
            ($this->publishPostAction)($post->id);
        } catch (PostException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bài viết đã được publish.');
    }
}
