<header class="mb-4">
    <nav class="navbar navbar-expand-md navbar-light bg-white">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img class="logo" alt="" src="{{ asset('storage/images/logo.png') }}">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav navbar-right">
                {{-- アプリ紹介ページへのリンク --}}
                <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-book-open fa-lg instruction_icon"></i>はじめに</a></li>

                {{-- ユーザー一覧へのリンク --}}
                <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-users fa-lg users_icon"></i>ユーザー一覧</a></li>

                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt fa-lg login_icon"></i>{{ __('Login') }}</a>
                    </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus fa-lg register_icon"></i>{{ __('Register') }}</a>
                    </li>
                @endif
                @else
                
                    {{-- 新規投稿ページへのリンク --}}
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-pen-square fa-lg new_post"></i>新規投稿</a></li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
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