<div class="container">
    <div class="row">
        <div class="justify-content-lg-end d-none d-lg-flex col-lg-5">
            @empty($user->avatar)
                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_show_avatar rounded-circle ml-5" width="230" height="230">
            @else
                <img src="{{ $user->avatar }}" class="rounded-circle ml-5 user_show_avatar" width="230" height="230">
            @endempty
        </div>

        <div class="d-flex justify-content-center d-lg-none d-flex col-12">
            @empty($user->avatar)
                <img src="{{ asset('storage/images/default_icon.png') }}" class="user_show_avatar rounded-circle mr-3" width="230" height="230">
            @else
                <img src="{{ $user->avatar }}" class="rounded-circle mr-3 user_show_avatar" width="230" height="230">
            @endempty
            @if(Auth::id() === $user->id)
              <div class="d-lg-none d-block align-self-end">{!! link_to_route('users.edit', '編集', ['user' => $user->id], ['class' => 'btn btn-outline-success rounded-pill fas fa-user-edit']) !!}</div>
            @endif
        </div>

        @if(Auth::id() === $user->id)
            <div class="flex-lg-column col-lg-6 pr-5 d-none d-lg-flex" style="max-width: 485px;">
                <div class="align-self-end">{!! link_to_route('users.edit', '編集', ['user' => $user->id], ['class' => 'btn btn-outline-success rounded-pill fas fa-user-edit']) !!}</div>
                <div class="font-weight-bold mt-3">{{ $user->name }}</div>
                <div class="pt-1 overflow-auto" style="max-height: 150px;">
                    <p>{!! nl2br(e($user->self_introduction)) !!}</p>
                </div>
            </div>
        @else
            <div class="flex-lg-column col-lg-6 pr-5 d-none d-lg-flex" style="max-width: 485px;">
                <div class="user_other font-weight-bold mt-3">{{ $user->name }}</div>
                <div class="overflow-auto mt-1" style="max-height: 150px;">
                    <p>{!! nl2br(e($user->self_introduction)) !!}</p>
                </div>
              </div>
        @endif
    </div>
    <div class="row justify-content-center d-lg-none d-flex mt-3">
        <div class="col-12 flex-column" style="max-width: 485px;">
            <div class="font-weight-bold">{{ $user->name }}</div>
            <div class="pt-3 overflow-auto" style="max-height: 150px;">
                <p>{!! nl2br(e($user->self_introduction)) !!}</p>
            </div>
        </div>
    </div>
</div>