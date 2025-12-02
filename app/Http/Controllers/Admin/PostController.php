<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\BulkPostRequest;
use App\Http\Requests\Post\FilterPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(protected PostService $postService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(FilterPostRequest $request): View
    {
        Gate::authorize('view-any', Post::class);
        $posts = $this->postService->list($request->validated());
        $users = User::pluck('name', 'id');
        $tags = Tag::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $status = PostStatus::options();

        return view('admin.posts.index', compact('posts', 'users', 'tags', 'categories', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Post::class);

        return view('admin.posts.form', [
            'post' => new Post,
            'categories' => Category::options(),
            'tags' => Tag::options(),
            'statusOptions' => PostStatus::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);
        $this->postService->create($request->toDto());

        return to_route('admin.posts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::options();
        $tags = Tag::options();
        $statusOptions = PostStatus::options();

        return view('admin.posts.form', compact('post', 'categories', 'tags', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);
        $this->postService->update($post, $request->toDto());

        return redirect($request->input('redirect', route('admin.posts.index')))
            ->with('success', 'Updated')
            ->with('highlight_id', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);
        $this->postService->delete($post);

        return to_route('admin.posts.index')->with('success', 'Deleted');
    }

    public function bulk(BulkPostRequest $request): RedirectResponse
    {
        $action = $request->validated('action');
        Gate::authorize('bulk', [Post::class, $action]);

        $ids = $request->ids();

        match ($action) {
            'delete' => $this->postService->bulkDelete($ids),
            'restore' => $this->postService->bulkRestore($ids),
            'force_delete' => $this->postService->bulkForceDelete($ids),
            'publish' => $this->postService->bulkPublish($ids),
            'unpublish' => $this->postService->bulkUnpublish($ids),
            default => null,
        };

        return redirect()->back()->with('success', 'Bulk action executed.');
    }
}
