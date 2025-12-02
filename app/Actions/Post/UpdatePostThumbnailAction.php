<?php

namespace App\Actions\Post;

use App\Models\Post;
use App\Repositories\Eloquent\PostRepository;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;

class UpdatePostThumbnailAction
{
    public function __construct(
        private UploadService $upload,
        private PostRepository $posts,
    ) {}

    public function execute(Post $post, UploadedFile $file): Post
    {
        // 1. Upload file
        $uploaded = $this->upload->upload($file, 'posts');

        // 2. Delete old thumbnail
        if ($post->thumbnail) {
            $this->upload->delete($post->thumbnail, 'posts');
        }

        // 3. Update DB
        $this->posts->update($post->id, [
            'thumbnail' => $uploaded->relative,
        ]);

        return $post->refresh();
    }
}
