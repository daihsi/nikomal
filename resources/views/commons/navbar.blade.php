<div class="container-fluid" style="max-width: 1700px;">
    <header class="mb-3">
        <nav class="navbar navbar-expand-lg navbar-light bg-white header_navbar">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img class="logo" alt="nikomalロゴ・トップページリンク" src="{{ asset('storage/images/logo.png') }}">
            </a>
            <a href="{{ route('posts.search') }}" class="mt-1 ml-2 mr-auto d-lg-none search_link">
                <span class="search_icon d-none d-sm-inline"><i class="fas fa-search fa-lg"></i>検索</span>
                <span class="search_icon d-sm-none"><i class="fas fa-search fa-lg"></i></span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- 右側 -->
                <ul class="navbar-nav mr-auto">
                    {{-- アプリ紹介ページへのリンク --}}
                    <li class="nav-item pt-1 d-none d-lg-inline">
                        <a class="nav-link" href="{{ route('posts.search') }}"><i class="fas fa-search fa-lg"></i>検索</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-right">
                    {{-- アプリ紹介ページへのリンク --}}
                    <li class="nav-item pt-2">
                        <a class="nav-link" href="#"><i class="fas fa-book-open fa-lg instruction_icon"></i>はじめに</a>
                    </li>

                    {{-- ユーザー一覧へのリンク --}}
                    <li class="nav-item pt-2">
                        <a class="nav-link" href="{{ route('users.index') }}"><i class="fas fa-users fa-lg users_icon"></i>ユーザー一覧</a>
                    </li>

                        <!-- Authentication Links -->
                    @guest
                        <li class="nav-item pt-2">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt fa-lg login_icon"></i>{{ __('Login') }}</a>
                        </li>
                    @if (Route::has('register'))
                        <li class="nav-item pt-2">
                            <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus fa-lg register_icon"></i>{{ __('Register') }}</a>
                        </li>
                    @endif
                    @else

                        {{-- 新規投稿ページへのリンク --}}
                        <li class="nav-item pt-2">
                            <a class="nav-link" href="{{ route('posts.create') }}"><i class="fas fa-pen-square fa-lg new_post"></i>新規投稿</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                @empty(Auth::user()->avatar)
                                    <img src="{{ asset('storage/images/default_icon.png') }}" class="nav_avatar_iconuser_avatar_icon rounded-circle " width="40" height="40"><span class="ml-1" id="navbar_user_name">{{ Auth::user()->name }}</span>
                                @else
                                    <img src="{{ Auth::user()->avatar }}" class="nav_avatar_iconuser_avatar_icon rounded-circle" width="40" height="40"><span class="ml-1" id="navbar_user_name">{{ Auth::user()->name }}</span>
                                @endempty
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-light" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('/') }}"><i class="fas fa-home fa-lg toppage_icon"></i>トップページ</a>
                                <a class="dropdown-item" href="{{ route('users.show', Auth::user()->id) }}"><i class="fas fa-address-card fa-lg profile_icon"></i>マイプロフィール</a>

                                {{-- 管理ユーザーはメールアドレス変更、パスワード変更フォームにアクセスできない --}}
                                @cannot('admin')
                                    <a class="dropdown-item" href="{{ route('email.request') }}"><i class="fas fa-cog fa-lg email_reset_icon"></i>メールアドレス再設定</a>
                                    <a class="dropdown-item" href="{{ route('password.request') }}"><i class="fas fa-key fa-lg password_reset_icon"></i>パスワード再設定</a>
                                @endcannot
                                <a class="dropdown-item logout_alert" href="#">
                                    <i class="fas fa-sign-out-alt fa-lg logout_icon"></i>
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>
    </header>
</div>