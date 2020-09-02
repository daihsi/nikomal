@if (count($posts) > 0)
<div class="container">
    <div class="row" id="post_card_container">
        <div class="post_sizer col-xl-4 col-lg-6 col-md-8 col-sm-10 col-12 offset-xl-0 offset-lg-0 offset-md-2 offset-sm-1"></div>
        @foreach ($posts as $post)
            <div class="post_item col-xl-4 col-lg-6 col-md-8 col-sm-10 col-12 offset-xl-0 offset-lg-0 offset-md-2 offset-sm-1">
            <div class="card shadow p-2 bg-white rounded mb-4">
                <div class="card-header">
                    @empty($post->user->avatar)
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark font-weight-bold"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</a>
                    @else
                        <a href="{{ route('users.show', $post->user->id) }}" class="text-dark font-weight-bold"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</a>
                    @endempty
                </div>
                <div class="card-body">
                    @foreach ($post->postImages as $post_image)
                        <a href="{{ route('posts.show', $post->id) }}"><img src="{{ $post_image->image }}" class="rounded post_image_card"
                            @foreach ($post->postCategorys as $post_category) @php $animals_name = 'animals_name'. $post->id; $$animals_name[] = $post_category->name; @endphp @endforeach alt="{{ implode('・', $$animals_name). 'の笑顔写真。投稿の詳細ページへ' }}">
                        </a>
                    @endforeach
                </div>
                <div class="card-footer">
                    <p class="card-text post_content">{{ $post->content }}</p>
                        @foreach($post->postCategorys as $post_category)
                            <a href="{{ route('posts.categorys', $post_category->id) }}"><span class="p_category"><i class="fas fa-hashtag p_hash"></i>{{ $post_category->name }}</span></a>
                        @endforeach
                    <div class="d-flex justify-content-center mt-2">
                        {{-- いいねボタン --}}
                        @include('likes.like_button')
                        <div class="ml-5 d-flex align-items-center">
                            <i class="far fa-comments fa-lg pr-2" style="color: #BBBBBB;"></i>
                            <span class="align-self-end comment_count"
                                @if (Auth::check())
                                    style="margin:0 0 1.6px 0;"
                                @else
                                    style="margin: 2px 0 0 0;"
                                @endif
                            >{{ $post->postComments->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <small>{{ $post->created_at->format('Y/m/d H:i') }}</small>
                    </div>
                </div>
            </div>
            </div>
        @endforeach
    </div>
</div>
<div class="page_load_status">
    <div class="loader-ellips infinite-scroll-request">
        <span class="loader-ellips__dot"></span>
        <span class="loader-ellips__dot"></span>
        <span class="loader-ellips__dot"></span>
        <span class="loader-ellips__dot"></span>
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
