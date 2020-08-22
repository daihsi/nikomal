@if (count($comments) > 0)
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12 col-md-11">
            <div class="comment_area" id="comment_area"><!-- コメント一覧全体を囲う-->
                @foreach ($comments as $comment)
                    @if (Auth::id() === $comment->user_id)
                        <!--右コメント(認証ユーザーのコメント)-->
                        <div class="balloon overflow-hidden my-2 w-100">
                            <div class="balloon_chatting w-100 text-right">
                                @empty($comment->user->avatar)
                                    <a href="{{ route('users.show', $comment->user_id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title">{{ $comment->user->name }}<img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle ml-1" width="40" height="40" alt="{{ $comment->user->name }}のアバター画像。詳細ぺージへのリンク"></div></a>
                                @else
                                    <a href="{{ route('users.show', $comment->user_id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title">{{ $comment->user->name }}<img src="{{ $comment->user->avatar }}" class="rounded-circle ml-1" width="40" height="40" alt="{{ $comment->user->name }}のアバター画像。詳細ぺージへのリンク"></div></a>
                                @endempty
                                <div class="authenticated_user_comment">
                                    <p>{!! nl2br(e($comment->comment)) !!}</p>
                                </div>
                                <form method="POST" action="{{ route('posts.uncomment', $comment->id) }}" class="mb-3">
                                    <small>{{ $comment->created_at->format('Y/m/d H:i') }}</small>
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-link comment_trash text-danger"><i class="far fa-trash-alt"></i>削除</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!--左コメント(認証ユーザー以外)-->
                        <div class="balloon overflow-hidden my-2 w-100">
                            <div class="balloon_chatting w-100 text-left">
                                @empty($comment->user->avatar)
                                    <a href="{{ route('users.show', $comment->user_id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle mr-1" width="40" height="40" alt="{{ $comment->user->name }}のアバター画像。詳細ぺージへのリンク">{{ $comment->user->name }}</div></a>
                                @else
                                    <a href="{{ route('users.show', $comment->user_id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="{{ $comment->user->avatar }}" class="rounded-circle mr-1" width="40" height="40" alt="{{ $comment->user->name }}のアバター画像。詳細ぺージへのリンク">{{ $comment->user->name }}</div></a>
                                @endempty
                                <div class="user_comment">
                                    <p>{!! nl2br(e($comment->comment)) !!}</p>
                                </div>
                                <div class="mb-4 ml-4">
                                    <small>{{ $comment->created_at->format('Y/m/d H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @if ($comments->hasMorePages())
        <p class="pagination">
            <a href="{{ $comments->nextPageUrl() }}" class="comment_next"></a>
        </p>
        <p class="text-center mt-3">
          <button class="comment_more_button btn btn-success btn-lg" aria-pressed="true">もっと見る</button>
        </p>
    @endif
@else
    <div class="d-flex justify-content-center align-items-center" style="height:200px; color:rgba(0,0,0,0.4);">
        まだコメントがありません
    </div>
@endif
