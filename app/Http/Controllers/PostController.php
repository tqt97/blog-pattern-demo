<?php

namespace App\Http\Controllers;

use App\Actions\Post\IncrementPostViewCountAction;
use App\Http\Requests\Post\FilterPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Jobs\IncrementPostViewJob;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected IncrementPostViewCountAction $incrementPostViewCountAction
    ) {}

    public function index(FilterPostRequest $request): View
    {
        $filter = $request->toFilter();
        $posts = $this->postService->list($filter);

        return view('posts.index', compact('posts', 'filter'));
    }

    public function show(string $slug): RedirectResponse|View
    {
        $post = $this->postService->getBySlug($slug);
        // ($this->incrementPostViewCountAction)($post->id);
        IncrementPostViewJob::dispatch($post->id);

        return view('posts.show', compact('post'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->postService->create($request->toDto());

        return to_route('posts.index')->with('success', 'Created');
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->update($post->id, $request->toDto());

        return back()->with('success', 'Updated');
    }

    public function destroy($id): RedirectResponse
    {
        $this->postService->delete($id);

        return back()->with('success', 'Deleted');
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->postService->publish($post->id);

        return back()->with('success', 'Post published');
    }
}
