<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\FilterTagRequest;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\HttpCache\Store;

class TagController extends Controller
{
    public function __construct(protected TagService $tagService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(FilterTagRequest $request)
    {
        Gate::authorize('view-any', Tag::class);
        $tags = $this->tagService->list($request->toFilter());

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Tag::class);

        return view('admin.tags.form', ['tag' => new Tag]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        Gate::authorize('create', Tag::class);
        $this->tagService->create($request->toDto());

        return to_route('admin.tags.index')->with('success', 'Created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        Gate::authorize('update', $tag);

        return view('admin.tags.form', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        Gate::authorize('update', $tag);
        $this->tagService->update($tag->id, $request->toDto());

        return to_route('admin.tags.index')->with('success', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('delete', $tag);
        $this->tagService->delete($tag->id);

        return to_route('admin.tags.index')->with('success', 'Deleted');
    }
}
