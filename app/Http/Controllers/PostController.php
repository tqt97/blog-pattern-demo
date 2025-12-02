<?php

namespace App\Http\Controllers;

use App\Actions\Post\IncrementPostViewCountAction;
use App\Cache\Domains\PostCache;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Jobs\IncrementPostViewJob;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected IncrementPostViewCountAction $incrementPostViewCountAction,
        protected PostCache $postCache
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->toFilter();
        $posts = $this->postService->list($filter);
        $topViewed = $this->postService->topViewed();
        $recent = $this->postService->recentPublished();

        return view('posts.index', compact('posts', 'filter'));
    }

    public function show(string $slug): View|Response
    {
        $post = $this->postService->getBySlug($slug);
        // $etag = md5($post->id.'|'.$post->updated_at);
        $etag = md5($post->id.'|'.$post->updated_at?->timestamp);

        if (request()->header('If-None-Match') === $etag) {
            return response()->noContent(Response::HTTP_NOT_MODIFIED)
                ->setEtag($etag);
        }

        $response = response()
            ->view('posts.show', compact('post'))
            ->setEtag($etag);

        // Optional: Last-Modified
        if ($post->updated_at) {
            $response->setLastModified($post->updated_at);
        }

        // Optional: cho trình duyệt + proxy cache
        $response->header('Cache-Control', 'public, max-age=60');

        IncrementPostViewJob::dispatch($post->id);

        return $response;
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->postService->create($request->toDto());

        return to_route('posts.index')->with('success', 'Created');
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->update($post, $request->toDto());

        return back()->with('success', 'Updated');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->delete($post);

        return back()->with('success', 'Deleted');
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->postService->publish($post);

        return back()->with('success', 'Post published');
    }
}
