@if (count($followers) > 0)
<div class="container">
    <div class="row mt-3 mx-auto" id="follower_list">
        @foreach ($followers as $follower)
            <div class="follower_card col-6 col-sm-6 col-lg-4">
                <div class="card border-0 d-none d-sm-block mb-5 text-center">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('users.show', ['user' => $follower->id]) }}">
                            @empty($follower->avatar)
                                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="155" height="155">
                            @else
                                <img src="{{ $follower->avatar }}" class="user_avatar_icon rounded-circle" width="155" height="155">
                            @endempty
                        </a>
                        <div class="d-flex flex-column ml-2 align-self-center">
                            <a href="{{ route('users.show', $follower->id) }}" class="btn btn-link text-dark font-weight-bold ">{{ $follower->name }}</a>
                            {{-- フォロー/アンフォローボタン--}}
                            @include('user_follow.follow_button_followers')
                        </div>
                    </div>
                </div>
                <div class="card border-0 d-sm-none d-block mb-5 text-center">
                    <div class="d-sm-flex justify-content-center">
                        <a href="{{ route('users.show', ['user' => $follower->id]) }}">
                            @empty($follower->avatar)
                                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="85" height="85">
                            @else
                                <img src="{{ $follower->avatar }}" class="user_avatar_icon rounded-circle" width="85" height="85">
                            @endempty
                        </a>
                        <div class="d-sm-flex flex-column ml-2 align-self-center">
                            <a href="{{ route('users.show', $follower->id) }}" class="btn btn-link text-dark font-weight-bold mr-auto">{{ $follower->name }}</a>
                            {{-- フォロー/アンフォローボタン--}}
                            @include('user_follow.follow_button_followers')
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@if ($followers->hasMorePages())
    <p class="pagination">
        <a href="{{ $followers->nextPageUrl() }}" class="follower_pagination_next"></a>
    </p>
    <p class="text-center mt-3">
      <button class="follower_more_button btn btn-success btn-lg" aria-pressed="true">もっと見る</button>
    </p>
@endif
@else
<div class="d-flex justify-content-center align-items-center" style="height:200px; color:rgba(0,0,0,0.4);">
    まだフォロワーがいません
</div>
@endif
