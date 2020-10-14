@if (count($users) > 0)
    <div class="row mt-4 mx-auto" id="user_list">
        @foreach ($users as $user)
            <div class="user_card col-sm-6 col-lg-12 col-xl-6">
                <div class="card border-0 mb-5 text-center">
                    <div class="d-flex flex-column justify-content-center">
                        <a href="{{ route('users.show', ['user' => $user->id]) }}">
                            @empty($user->avatar)
                                <img src="{{ asset('/images/default_icon.png') }}" class="post_user_avatar_icon rounded-circle" width="130" height="130">
                            @else
                                <img src="{{ $user->avatar }}" class="post_user_avatar_icon rounded-circle" width="130" height="130">
                            @endempty
                        </a>
                        <div class="d-flex flex-column ml-2 align-self-center">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-link text-dark font-weight-bold like_user_name">{{ $user->name }}</a>
                            {{-- フォロー/アンフォローボタン--}}
                            @include('user_follow.follow_button')
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="page_load_status">
        <div class="loader-ellips infinite-scroll-request">
            <span class="loader-ellips__dot"></span>
            <span class="loader-ellips__dot"></span>
            <span class="loader-ellips__dot"></span>
            <span class="loader-ellips__dot"></span>
        </div>
    </div>
@if ($users->hasMorePages())
    <p class="pagination">
        <a href="{{ $users->nextPageUrl() }}" class="pagination_next"></a>
    </p>
    <p class="text-center mt-3">
      <button class="view_more_button btn btn-success btn-lg" aria-pressed="true">もっと見る</button>
    </p>
@endif
@else
    <div class="d-flex justify-content-center align-items-center" style="height:200px; color:rgba(0,0,0,0.4);">
        まだいいねされていません
    </div>
@endif
