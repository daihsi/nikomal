@if (count($users) > 0)

<div class="container">
    <div class="card-group mx-auto">
        <div class="row">
            @foreach ($users as $user)
                <div class="card border-0 col-sm-6 col-md-4 col-xl-3 d-none d-sm-block"　style="width: 100px">
                    <div class="card-body">
                    <a href="{{ route('users.show', ['user' => $user->id]) }}">
                        @empty($user->avatar)
                            <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="200" height="200">
                        @else
                            <img src="{{ $user->avatar }}" class="user_avatar_icon rounded-circle" width="200" height="200">
                        @endempty
                    </a>
                        {!! link_to_route('users.show', $user->name, ['user' => $user->id], ['class' => 'btn btn-link text-dark font-weight-bold']) !!}
                    </div>
                </div>
                <div class="card border-0 col-6 d-sm-none d-block">
                    <div class="card-body">
                    <a href="{{ route('users.show', ['user' => $user->id]) }}">
                        @empty($user->avatar)
                            <img src="{{ asset('storage/images/default_icon.png') }}" class="user_avatar_icon rounded-circle" width="70" height="70">
                        @else
                            <img src="{{ $user->avatar }}" class="user_avatar_icon rounded-circle" width="70" height="70">
                        @endempty
                    </a>
                        {!! link_to_route('users.show', $user->name, ['user' => $user->id], ['class' => 'btn btn-link text-dark font-weight-bold']) !!}
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endif