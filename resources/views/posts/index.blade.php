<div class="row">
    <div class="col-md-8">
        @foreach ($posts as $post)
            @include('posts._item', ['post' => $post])
        @endforeach

        {{ $posts->links() }}
    </div>

    <div class="col-md-4">

        <h4>🔥 Top viewed</h4>
        <ul>
            @foreach ($topViewed as $item)
                <li><a href="{{ route('blog.show', $item->slug) }}">{{ $item->title }}</a></li>
            @endforeach
        </ul>

        <h4>🆕 Recent posts</h4>
        <ul>
            @foreach ($recent as $item)
                <li><a href="{{ route('blog.show', $item->slug) }}">{{ $item->title }}</a></li>
            @endforeach
        </ul>

    </div>
</div>
