<ul class="nav nav-tabs justify-content-center mb-4">
    {{-- ユーザ投稿一覧タブ --}}
    <li class="nav-item">
        <a href="{{ route('users.show', ['user' => $user->id]) }}" class="nav-link {{ Request::routeIs('users.show') ? 'active' : '' }} text-dark">
            投稿
            <span class="badge badge-white badge-pill">{{ $user->posts_count }}</span>
        </a>
    </li>
    {{-- フォロー一覧タブ --}}
    <li class="nav-item">
        <a href="{{ route('users.followings', ['id' => $user->id]) }}" class="nav-link {{ Request::routeIs('users.followings') ? 'active' : '' }} text-dark">
            フォロー
            <span class="badge badge-white badge-pill">{{ $user->followings_count }}</span>
        </a>
    </li>
    {{-- フォロワー一覧タブ --}}
    <li class="nav-item">
        <a href="{{ route('users.followers', ['id' => $user->id]) }}" class="nav-link {{ Request::routeIs('users.followers') ? 'active' : '' }} text-dark">
            フォロワー
            <span class="badge badge-white badge-pill">{{ $user->followers_count }}</span>
        </a>
    </li>
    {{-- いいえね一覧タブ --}}
    <li class="nav-item">
        <a href="{{ route('users.likes', ['id' => $user->id]) }}" class="nav-link {{ Request::routeIs('users.likes') ? 'active' : '' }} text-dark">
            いいね
            <span class="badge badge-white badge-pill">{{ $user->likes_count }}</span>
        </a>
    </li>
</ul>