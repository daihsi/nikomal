@extends('layouts.app')

@section('content')

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
              <div class="flex-lg-column align-self-center col-lg-6 pr-5 d-none d-lg-flex" style="max-width: 485px;">
                  <div class="font-weight-bold mt-3">{{ $user->name }}</div>
                  <div class="overflow-auto mt-1" style="max-height: 150px;">
                    <p>{!! nl2br(e($user->self_introduction)) !!}</p>
                  </div>
              </div>
            @endif

       </div>
       <div class="row justify-content-center d-lg-none d-flex">
            <div class="col-12 flex-column ml-5" style="max-width: 485px;">
                <div class="font-weight-bold">{{ $user->name }}</div>
                <div class="pt-3 overflow-auto" style="max-height: 150px;">
                  <p>{!! nl2br(e($user->self_introduction)) !!}</p>
                </div>
            </div>
       </div>
    </div>

    <div class="container-fluid">
    <div class="row justify-content-center">
    <div class="col-10">
    <ul class="nav nav-tabs justify-content-center mt-3" id="myTab">
      <li class="nav-item" role="presentation">
        <a class="nav-link text-muted active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">ホーム</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link text-muted" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">プロフィール</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link text-muted" id="messages-tab" data-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">メッセージ</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link text-muted" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">セッティング</a>
      </li>
    </ul>
    
    <div class="tab-content">
      <div class="tab-pane active" id="home" aria-labelledby="home-tab">ホーム</div>
      <div class="tab-pane" id="profile" aria-labelledby="profile-tab">プロフィール</div>
      <div class="tab-pane" id="messages" aria-labelledby="messages-tab">メッセージ</div>
      <div class="tab-pane" id="settings" aria-labelledby="settings-tab">セッティング</div>
    </div>
    </div>
    </div>
    </div>
@endsection