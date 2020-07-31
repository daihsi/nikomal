<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <ul class="nav nav-tabs justify-content-center mt-3 mb-3" id="myTab">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-muted active" id="posts_tab" data-toggle="tab" href="#posts" role="tab" aria-controls="posts" aria-selected="true">
                        投稿
                        <span class="badge badge-white badge-pill">{{ $user->posts_count }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-muted" id="followings_tab" data-toggle="tab" href="#followings" role="tab" aria-controls="followings" aria-selected="false">
                        フォロー
                        <span class="badge badge-white badge-pill">{{ $user->followings_count }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-muted" id="followers_tab" data-toggle="tab" href="#followers" role="tab" aria-controls="followers" aria-selected="false">
                        フォロワー
                        <span class="badge badge-white badge-pill">{{ $user->followers_count }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-muted" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">
                        いいね
                        {{-- <span class="badge badge-white badge-pill">{{ $user-> }}</span> --}}
                    </a>
                 </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="posts" aria-labelledby="posts_tab">
                    {{-- 自分の投稿一覧 --}}
                    @include('posts.posts')
                </div>
                <div class="tab-pane" id="followings" aria-labelledby="followings_tab">
                    {{-- フォロー一覧 --}}
                    @include('user_follow.followings')
                </div>
                <div class="tab-pane" id="followers" aria-labelledby="followers_tab">
                    {{-- フォロワー一覧 --}}
                    @include('user_follow.followers')
                </div>
                 <div class="tab-pane" id="settings" aria-labelledby="settings-tab">セッティング</div>
            </div>
        </div>
    </div>
</div>