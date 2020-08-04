@if (count($posts) > 0)
<div class="container">
    <div class="row" id="post_card_container">
        <div class="post_sizer col-xl-4 col-lg-6 col-md-8 col-sm-10 col-12 offset-xl-0 offset-lg-0 offset-md-2 offset-sm-1"></div>
        @foreach ($posts as $post)
            <div class="post_item col-xl-4 col-lg-6 col-md-8 col-sm-10 col-12 offset-xl-0 offset-lg-0 offset-md-2 offset-sm-1">
            <div class="card shadow p-2 bg-white rounded mb-4">
                @foreach ($post->postImages as $post_image)
                    <a href="{{ route('posts.show', $post->id) }}"><img src="{{ $post_image->image }}" class="card-img-top post_image_card"
                        @foreach ($post->postCategorys as $post_category) @php $animals_name = 'animals_name'. $post->id; $$animals_name[] = $post_category->name; @endphp @endforeach alt="{{ implode('・', $$animals_name). 'の笑顔写真。投稿の詳細ページへ' }}">
                    </a>
                @endforeach
                <div class="card-body">
                    <div class="card-title font-weight-bold">
                    @empty($post->user->avatar)
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle" width="40" height="40">{{ $post->user->name }}</a>
                    @else
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</a>
                    @endempty
                    </div>
                    <p class="card-text">{!! nl2br(e($post->content)) !!}</p>
                        @foreach($post->postCategorys as $post_category)
                            <div class="badge badge-success mr-1 align-self-center"><i class="fas fa-hashtag">{{ $post_category->name }}</i></div>
                        @endforeach
                    <div class="d-flex justify-content-end">
                        @include('likes.like_button')
                    </div>
                </div>
            </div>
            </div>
        @endforeach
    </div>
</div>
@if ($posts->hasMorePages())
    <p class="pagination">
        <a href="{{ $posts->nextpageUrl() }}" class="pagination_next"></a>
    </p>
    <p class="text-center mt-3">
        <button class="view_more_button btn btn-success btn-lg" aria-pressed="true">もっと見る</button>
    </p>
@endif
@endif
