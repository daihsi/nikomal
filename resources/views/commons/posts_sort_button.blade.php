<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-sm-11 mt-3 mb-5 d-inline-flex">
            @if (Request::is('/'))
                <a href="{{ url('/') }}" class="btn btn-primary btn-block mr-4" role="button">新着投稿</a>
                <a href="{{ route('posts.popular') }}" class="btn btn-outline-secondary btn-block posts_sort" role="button">人気投稿</a>
            @elseif (Request::routeIs('posts.popular'))
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-block mr-4 posts_sort" role="button">新着投稿</a>
                <a href="{{ route('posts.popular') }}" class="btn btn-primary btn-block" role="button">人気投稿</a>
            @endif
        </div>
    </div>
</div>