@if (count($posts) > 0)
<div class="container">
    <div class="row" id="post_card_container">
        <div class="post_sizer col-md-4 col-sm-6 col-12"></div>
        @foreach ($posts as $post)
            <div class="post_item col-md-4 col-sm-6 col-12 mb-2" >
            <div class="card shadow p-2 mb-5 bg-white rounded mb-4">
                @foreach ($post->postImages as $post_image)
                    <a href="{{ route('posts.show', $post->id) }}"><img src="{{ $post_image->image }}" class="card-img-top post_image_card"  style="width:100%;"
                        @foreach ($post->postCategorys as $post_category) @php $animals_name = 'animals_name'. $post->id; $$animals_name[] = $post_category->name; @endphp @endforeach alt="{{ implode('・', $$animals_name). 'の笑顔写真。投稿の詳細ページへ' }}">
                    </a>
                @endforeach
                <div class="card-body">
                    @empty($post->user->avatar)
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark"><h5 class="card-title"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle" width="40" height="40">{{ $post->user->name }}</h5></a>
                    @else
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark"><h5 class="card-title font-weight-bold"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</h5></a>
                    @endempty
                    <p class="card-text">{!! nl2br(e($post->content)) !!}</p>
                    @foreach($post->postCategorys as $post_category)
                        <span class="badge badge-success"><i class="fas fa-hashtag">{{ $post_category->name }}</i></span>
                    @endforeach
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
