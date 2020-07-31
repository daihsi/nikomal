@if(Auth::check())
    @if(Auth::id() != $follower->id)
        @if(Auth::user()->isFollowing($follower->id))
            {{-- アンフォローボタン --}}
            <form method="POST" action="{{ route('user.unfollow', $follower->id) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-primary btn-sm rounded-pill follow_button"><span class="unfollow_button">フォロー解除</span><span class="follow_now_button">フォロー中</span></button>
            </form>
        @else
            {{-- フォローボタン --}}
            <form method="POST" action="{{ route('user.follow', $follower->id) }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>
            </form>
        @endif
    @else
    @endif
@endif