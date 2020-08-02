@if (count($users) > 0)
<div class="container">
    <div class="row mt-5 mx-auto" id="user_list">
        @foreach ($users as $user)
            <div class="user_card col-6 col-sm-6 col-lg-4">
                <div class="card border-0 d-none d-sm-block mb-5 text-center">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('users.show', ['user' => $user->id]) }}">
                            @empty($user->avatar)
                                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="155" height="155">
                            @else
                                <img src="{{ $user->avatar }}" class="user_avatar_icon rounded-circle" width="155" height="155">
                            @endempty
                        </a>
                        <div class="d-flex flex-column ml-2 align-self-center">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-link text-dark font-weight-bold ">{{ $user->name }}</a>
                            {{-- フォロー/アンフォローボタン--}}
                            @include('user_follow.follow_button')
                        </div>
                    </div>
                </div>
                <div class="card border-0 d-sm-none d-block mb-5 text-center">
                    <div class="d-sm-flex justify-content-center">
                        <a href="{{ route('users.show', ['user' => $user->id]) }}">
                            @empty($user->avatar)
                                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="85" height="85">
                            @else
                                <img src="{{ $user->avatar }}" class="user_avatar_icon rounded-circle" width="85" height="85">
                            @endempty
                        </a>
                        <div class="d-sm-flex flex-column ml-2 align-self-center">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-link text-dark font-weight-bold mr-auto">{{ $user->name }}</a>
                            {{-- フォロー/アンフォローボタン--}}
                            @include('user_follow.follow_button')
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
@endif
