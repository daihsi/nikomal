@if(Auth::check() && !Gate::allows('admin'))
    @if(Auth::id() != $user->id)
        @if(Auth::user()->isFollowing($user->id))
            {{-- アンフォローボタン --}}
            <div>
                <button type="button" class="follow btn btn-primary btn-sm rounded-pill follow_button" data-id="{{ $user->id }}">
                    フォロー中
                </button>
            </div>
        @else
            {{-- フォローボタン --}}
            <div>
                <button type="button" class="follow btn btn-outline-primary btn-sm rounded-pill" data-id="{{ $user->id }}">
                    フォロー
                </button>
            </div>
        @endif
    @endif
@endif